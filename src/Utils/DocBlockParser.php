<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

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

    public function extractExampleDescription(string $docblock): ?string
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

    public function isDeprecated(?string $docblock): bool
    {
        $docNode = $this->parsePhpDoc($docblock);
        if (!$docNode instanceof PhpDocNode) {
            return false;
        }

        return $docNode->getDeprecatedTagValues() !== [];
    }
}
