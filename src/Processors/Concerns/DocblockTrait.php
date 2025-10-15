<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors\Concerns;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;
use OpenApi\Generator;

trait DocblockTrait
{
    /**
     * An annotation is a docblock root if it is the top-level / outermost annotation in a PHP docblock.
     */
    public function isDocblockRoot(OA\AbstractAnnotation $annotation): bool
    {
        if (!$annotation->_context) {
            return true;
        }

        if (1 == count($annotation->_context->annotations)) {
            return true;
        }

        /** @var array<class-string,bool> $matchPriorityMap */
        $matchPriorityMap = [
            OA\OpenApi::class,

            OA\Operation::class => false,
            OA\Property::class => false,
            OA\Parameter::class => false,
            OA\Response::class => false,

            OA\Schema::class => true,
            OAT\Schema::class => true,
        ];
        // try to find the best root match
        foreach ($matchPriorityMap as $className => $strict) {
            foreach ($annotation->_context->annotations as $contextAnnotation) {
                if ($strict) {
                    if ($className === get_class($contextAnnotation)) {
                        return $annotation === $contextAnnotation;
                    }
                } else {
                    if ($contextAnnotation instanceof $className) {
                        return $annotation === $contextAnnotation;
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
     * Parse a docblock and return the full content/text.
     */
    public function parseDocblock(?string $docblock, ?array &$tags = null): string
    {
        if (Generator::isDefault($docblock)) {
            return Generator::UNDEFINED;
        }

        $comment = preg_split('/(\n|\r\n)/', (string) $docblock);
        $comment[0] = preg_replace('/[ \t]*\\/\*\*/', '', $comment[0]); // strip '/**'
        $ii = count($comment) - 1;
        $comment[$ii] = preg_replace('/\*\/[ \t]*$/', '', $comment[$ii]); // strip '*/'
        $lines = [];
        $append = false;
        $skip = false;
        foreach ($comment as $line) {
            $line = preg_replace('/^\s+\* ?/', '', $line);
            if (substr($tagline = trim($line), 0, 1) === '@') {
                $this->handleTag($tagline, $tags);
                $skip = true;
            }
            if ($skip) {
                continue;
            }
            if ($append) {
                $ii = count($lines) - 1;
                $lines[$ii] = substr($lines[$ii], 0, -1) . $line;
            } else {
                $lines[] = $line;
            }
            $append = (substr($line, -1) === '\\');
        }

        $description = trim(implode("\n", $lines));

        return $description === ''
            ? Generator::UNDEFINED
            : $description;
    }

    /**
     * A short piece of text, usually one line, providing the basic function of the associated element.
     *
     * @param string $content The full docblock content
     */
    public function extractCommentSummary(string $content): string
    {
        if ($content === Generator::UNDEFINED) {
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
     * @param string $content The full docblock content
     */
    public function extractCommentDescription(string $content): string
    {
        if ($content === Generator::UNDEFINED) {
            return Generator::UNDEFINED;
        }

        $summary = $this->extractCommentSummary($content);
        if ($summary === Generator::UNDEFINED) {
            return Generator::UNDEFINED;
        }

        $description = '';
        if (false !== ($substr = substr($content, strlen($summary)))) {
            $description = trim($substr);
        }

        return $description ?: Generator::UNDEFINED;
    }

    /**
     * Extract property type and description from a <code>@var</code> dockblock line.
     *
     * @return array{type: ?string, description: ?string}
     */
    public function parseVarLine(?string $docblock): array
    {
        $comment = str_replace("\r\n", "\n", (string) $docblock);
        $comment = preg_replace('/\*\/[ \t]*$/', '', $comment); // strip '*/'

        preg_match('/@var\s+(?<type>[^\s]+)([ \t])?(?<description>.+)?+$/im', $comment, $matches);

        $result = array_merge(
            ['type' => null, 'description' => null],
            array_filter($matches, fn ($key): bool => in_array($key, ['type', 'description']), ARRAY_FILTER_USE_KEY)
        );

        return array_map(fn (?string $value): ?string => null !== $value ? trim($value) : null, $result);
    }

    /**
     * Extract example text from a <code>@example</code> dockblock line.
     */
    public function extractExampleDescription(string $docblock): ?string
    {
        if (!$docblock || $docblock === Generator::UNDEFINED) {
            return null;
        }

        preg_match('/@example\s+([ \t])?(?<example>.+)?$/im', $docblock, $matches);

        return $matches['example'] ?? null;
    }

    /**
     * Returns true if the <code>\@deprecated</code> tag is present, false otherwise.
     */
    public function isDeprecated(?string $docblock): bool
    {
        if (!$docblock || $docblock === Generator::UNDEFINED) {
            return false;
        }

        return 1 === preg_match('/@deprecated\s+([ \t])?(?<deprecated>.+)?$/im', $docblock);
    }
}
