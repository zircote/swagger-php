<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors\Concerns;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;
use OpenApi\Generator;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\ParserConfig;

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
                    if ($className === $contextAnnotation::class) {
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

    /**
     * Parse a docblock string into a PhpDocNode.
     */
    protected function parsePhpDoc(?string $docblock): ?PhpDocNode
    {
        if (!$docblock || Generator::isDefault($docblock)) {
            return null;
        }

        // Normalize single-star comments to PHPDoc format
        $normalized = preg_replace('#^/\*(?!\*)#', '/**', $docblock);

        // Ensure docblock has proper closing
        if (!str_contains((string) $normalized, '*/')) {
            $normalized = rtrim((string) $normalized) . '/';
        }

        $config = new ParserConfig([]);
        $lexer = new Lexer($config);
        $phpDocParser = new PhpDocParser(
            $config,
            new TypeParser($config, $constExprParser = new ConstExprParser($config)),
            $constExprParser,
        );

        try {
            $tokens = new TokenIterator($lexer->tokenize($normalized));

            return $phpDocParser->parse($tokens);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Format a type node as a compact string (without wrapping parentheses for union/intersection types).
     */
    protected function formatType(TypeNode $typeNode): string
    {
        if ($typeNode instanceof UnionTypeNode) {
            return implode('|', array_map(strval(...), $typeNode->types));
        }

        if ($typeNode instanceof IntersectionTypeNode) {
            return implode('&', array_map(strval(...), $typeNode->types));
        }

        return (string) $typeNode;
    }

    /**
     * Parse a docblock and return the full content/text.
     */
    public function parseDocblock(?string $docblock, ?array &$tags = null): string
    {
        $docNode = $this->parsePhpDoc($docblock);
        if (!$docNode) {
            return Generator::UNDEFINED;
        }

        // Extract @param tags if requested
        if (null !== $tags) {
            if (!array_key_exists('param', $tags)) {
                $tags['param'] = [];
            }
            foreach ($docNode->getParamTagValues() as $param) {
                $name = ltrim((string) $param->parameterName, '$');
                $tags['param'][$name] = [
                    'type' => (string) $param->type ?: null,
                    'description' => $param->description !== '' ? $param->description : null,
                ];
            }
            foreach ($docNode->getTypelessParamTagValues() as $param) {
                $name = ltrim((string) $param->parameterName, '$');
                $tags['param'][$name] = [
                    'type' => null,
                    'description' => $param->description !== '' ? $param->description : null,
                ];
            }
        }

        // Extract description from text nodes before first tag
        $lines = [];
        foreach ($docNode->children as $child) {
            if ($child instanceof PhpDocTagNode) {
                break;
            }
            if ($child instanceof PhpDocTextNode && $child->text !== '') {
                $lines[] = $child->text;
            }
        }

        $description = trim(implode("\n", $lines));
        // Handle line continuation with trailing backslash
        $description = preg_replace('/\\\\\n/', '', $description);

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
        if (Generator::isDefault($content)) {
            return Generator::UNDEFINED;
        }

        $lines = preg_split('/(\n|\r\n)/', $content);
        $summary = '';
        foreach ($lines as $line) {
            $summary .= $line . "\n";
            if ($line === '' || str_ends_with($line, '.')) {
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
     * An optional longer piece of text providing more details on the associated element's function.
     *
     * @param string $content The full docblock content
     */
    public function extractCommentDescription(string $content): string
    {
        if (Generator::isDefault($content)) {
            return Generator::UNDEFINED;
        }

        $summary = $this->extractCommentSummary($content);
        if (Generator::isDefault($summary)) {
            return Generator::UNDEFINED;
        }

        $description = '';
        if (($substr = substr($content, strlen((string) $summary))) !== '') {
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
        $result = ['type' => null, 'description' => null];

        $docNode = $this->parsePhpDoc($docblock);
        if (!$docNode) {
            return $result;
        }

        $varTags = $docNode->getVarTagValues();
        if ($varTags) {
            $varTag = reset($varTags);
            $type = $this->formatType($varTag->type);

            $result['type'] = $type !== '' ? $type : null;
            $result['description'] = $varTag->description !== '' ? trim((string) $varTag->description) : null;
        }

        return $result;
    }

    /**
     * Extract example text from a <code>@example</code> dockblock line.
     */
    public function extractExampleDescription(string $docblock): ?string
    {
        $docNode = $this->parsePhpDoc($docblock);
        if (!$docNode) {
            return null;
        }

        foreach ($docNode->getTagsByName('@example') as $tag) {
            $value = (string) $tag->value;

            return $value !== '' ? trim($value) : null;
        }

        return null;
    }

    /**
     * Returns true if the <code>@deprecated</code> tag is present, false otherwise.
     */
    public function isDeprecated(?string $docblock): bool
    {
        $docNode = $this->parsePhpDoc($docblock);
        if (!$docNode) {
            return false;
        }

        return count($docNode->getDeprecatedTagValues()) > 0;
    }
}
