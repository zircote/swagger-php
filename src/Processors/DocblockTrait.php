<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Annotations\Operation;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Schema as AnnotationSchema;
use OpenApi\Attributes\Schema as AttributeSchema;
use OpenApi\Generator;

trait DocblockTrait
{
    /**
     * An annotation is a root if it is the top-level / outermost annotation in a PHP docblock.
     */
    public function isRoot(AbstractAnnotation $annotation): bool
    {
        if (!$annotation->_context) {
            return true;
        }

        if (1 == count($annotation->_context->annotations)) {
            return true;
        }

        // find best match
        $matchPriorityMap = [
            Operation::class => false,
            Property::class => false,
            Parameter::class => false,
            AnnotationSchema::class => true,
            AttributeSchema::class => true,
        ];
        foreach ($matchPriorityMap as $className => $strict) {
            foreach ($annotation->_context->annotations as $contextAnnotation) {
                if ($strict) {
                    if ($className == get_class($contextAnnotation)) {
                        return  $annotation === $contextAnnotation;
                    }
                } else {
                    if ($contextAnnotation instanceof $className) {
                        return  $annotation === $contextAnnotation;
                    }
                }
            }
        }

        return false;
    }

    protected function handleTag(string $line, ?array &$tags = null): void
    {
        if (null === $tags) {
            return;
        }

        // split of tag name
        $token = preg_split("@[\s+　]@u", $line, 2);
        if (2 == count($token)) {
            $tag = substr($token[0], 1);
            $tail = $token[1];
            if (!array_key_exists($tag, $tags)) {
                $tags[$tag] = [];
            }

            if (false !== ($dpos = strpos($tail, '$'))) {
                $type = trim(substr($tail, 0, $dpos));
                $token = preg_split("@[\s+　]@u", substr($tail, $dpos), 2);
                $name = trim(substr($token[0], 1));
                $description = 2 == count($token) ? trim($token[1]) : null;

                $tags[$tag][$name] = [
                    'type' => $type,
                    'description' => $description,
                ];
            }
        }
    }

    /**
     * The text contents of the phpdoc comment (excl. tags).
     */
    public function extractContent(?string $docblock, ?array &$tags = null): string
    {
        if (Generator::isDefault($docblock)) {
            return Generator::UNDEFINED;
        }

        $comment = preg_split('/(\n|\r\n)/', (string) $docblock);
        $comment[0] = preg_replace('/[ \t]*\\/\*\*/', '', $comment[0]); // strip '/**'
        $i = count($comment) - 1;
        $comment[$i] = preg_replace('/\*\/[ \t]*$/', '', $comment[$i]); // strip '*/'
        $lines = [];
        $append = false;
        $skip = false;
        foreach ($comment as $line) {
            $line = ltrim($line, "\t *");
            if (substr($line, 0, 1) === '@') {
                $this->handleTag($line, $tags);
                $skip = true;
            }
            if ($skip) {
                continue;
            }
            if ($append) {
                $i = count($lines) - 1;
                $lines[$i] = substr($lines[$i], 0, -1) . $line;
            } else {
                $lines[] = $line;
            }
            $append = (substr($line, -1) === '\\');
        }
        $description = trim(implode("\n", $lines));
        if ($description === '') {
            return Generator::UNDEFINED;
        }

        return $description;
    }

    /**
     * A short piece of text, usually one line, providing the basic function of the associated element.
     */
    public function extractSummary(?string $docblock): string
    {
        if (!$content = $this->extractContent($docblock)) {
            return Generator::UNDEFINED;
        }
        $lines = preg_split('/(\n|\r\n)/', $content);
        $summary = '';
        foreach ($lines as $line) {
            $summary .= $line . "\n";
            if ($line === '' || substr($line, -1) === '.') {
                return trim($summary);
            }
        }
        $summary = trim($summary);
        if ($summary === '') {
            return Generator::UNDEFINED;
        }

        return $summary;
    }

    /**
     * An optional longer piece of text providing more details on the associated element’s function.
     *
     * This is very useful when working with a complex element.
     */
    public function extractDescription(?string $docblock): string
    {
        $summary = $this->extractSummary($docblock);
        if (!$summary) {
            return Generator::UNDEFINED;
        }

        $description = '';
        if (false !== ($substr = substr($this->extractContent($docblock), strlen($summary)))) {
            $description = trim($substr);
        }

        return $description ?: Generator::UNDEFINED;
    }
}
