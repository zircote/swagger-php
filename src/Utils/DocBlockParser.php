<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use OpenApi\Type\TypeMapper;
use OpenApi\Undefined;
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

class DocBlockParser
{
    public function parsePhpDoc(?string $docblock): ?PhpDocNode
    {
        if (!$docblock || Undefined::isDefault($docblock)) {
            return null;
        }

        $normalized = preg_replace('#^/\*(?!\*)#', '/**', $docblock);

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

    public function formatType(TypeNode $typeNode): string
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
     * @param array{param?: array<string, array{type: string|null, description: string|null}>}|null $tags
     *                                                                                                    collected tag values, by tag name; only `param` is populated today
     */
    public function parseDocblock(?string $docblock, ?array &$tags = null): string
    {
        $docNode = $this->parsePhpDoc($docblock);
        if (!$docNode instanceof PhpDocNode) {
            return Undefined::UNDEFINED;
        }

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
        $description = preg_replace('/\\\\\n/', '', $description);

        return $description === ''
            ? Undefined::UNDEFINED
            : $description;
    }

    public function extractCommentSummary(string $content): string
    {
        if (Undefined::isDefault($content)) {
            return Undefined::UNDEFINED;
        }

        $lines = preg_split('/(\n|\r\n)/', $content) ?: [];
        $summary = '';
        foreach ($lines as $line) {
            $summary .= $line . "\n";
            if ($line === '' || str_ends_with($line, '.')) {
                return trim($summary);
            }
        }
        $summary = trim($summary);
        if ($summary === '') {
            return Undefined::UNDEFINED;
        }

        return $summary;
    }

    public function extractCommentDescription(string $content): string
    {
        if (Undefined::isDefault($content)) {
            return Undefined::UNDEFINED;
        }

        $summary = $this->extractCommentSummary($content);
        if (Undefined::isDefault($summary)) {
            return Undefined::UNDEFINED;
        }

        $description = '';
        if (($substr = substr($content, strlen($summary))) !== '') {
            $description = trim($substr);
        }

        return $description ?: Undefined::UNDEFINED;
    }

    /**
     * @return array{type: ?string, description: ?string}
     */
    public function parseVarLine(?string $docblock): array
    {
        $result = ['type' => null, 'description' => null];

        $docNode = $this->parsePhpDoc($docblock);
        if (!$docNode instanceof PhpDocNode) {
            return $result;
        }

        $varTags = $docNode->getVarTagValues();
        if ($varTags !== []) {
            $varTag = reset($varTags);
            $type = $this->formatType($varTag->type);

            $result['type'] = $type !== '' ? $type : null;
            $result['description'] = $varTag->description !== '' ? trim((string) $varTag->description) : null;
        }

        return $result;
    }

    public function extractExample(string $docblock): ?string
    {
        $docNode = $this->parsePhpDoc($docblock);
        if (!$docNode instanceof PhpDocNode) {
            return null;
        }

        foreach ($docNode->getTagsByName('@example') as $tag) {
            $value = (string) $tag->value;

            return $value !== '' ? trim($value) : null;
        }

        return null;
    }

    /**
     * `$example` decoded as JSON where the value is one of `$types`, or `null` where the schema
     * is nullable. Anything else, and any example of a schema that has no type or allows a
     * string, stays the string written.
     *
     * @param array<string> $types
     */
    public function castExample(string $example, array $types, bool $nullable): mixed
    {
        if ($types === [] || in_array('string', $types, true)) {
            return $example;
        }

        try {
            $value = json_decode($example, flags: JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
            $decoded = json_decode($example, flags: JSON_THROW_ON_ERROR);

            /*
             * Reject non-finite numbers and integers that would become strings, including nested values.
             */
            if (json_encode($value, JSON_THROW_ON_ERROR) !== json_encode($decoded, JSON_THROW_ON_ERROR)) {
                return $example;
            }
        } catch (\JsonException) {
            return $example;
        }

        $valueType = match (true) {
            $value === null => 'null',
            $value instanceof \stdClass => 'object',
            is_float($value) && floor($value) === $value => 'integer',
            default => TypeMapper::valueType($value),
        };

        if (in_array($valueType, $types, true)
            || ($valueType === 'integer' && in_array('number', $types, true))
            || ($valueType === 'null' && $nullable)
        ) {
            return $value;
        }

        return $example;
    }

    public function isDeprecated(?string $docblock): bool
    {
        $docNode = $this->parsePhpDoc($docblock);
        if (!$docNode instanceof PhpDocNode) {
            return false;
        }

        return $docNode->getDeprecatedTagValues() !== [];
    }
}
