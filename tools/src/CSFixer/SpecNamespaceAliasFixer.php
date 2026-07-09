<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\CSFixer;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

class SpecNamespaceAliasFixer extends AbstractFixer
{
    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    public function fix(\SplFileInfo $file, Tokens $tokens): void
    {
        $hasAlias = false;
        $namespaceImport = null;
        $individualImports = [];

        for ($i = 0, $count = $tokens->count(); $i < $count; $i++) {
            if (!$tokens[$i]->isGivenKind(T_USE)) {
                continue;
            }

            $prev = $tokens->getPrevMeaningfulToken($i);
            if ($prev !== null && $tokens[$prev]->equals(')')) {
                continue;
            }

            $semicolonIndex = $tokens->getNextTokenOfKind($i, [';']);
            if ($semicolonIndex === null) {
                continue;
            }

            $content = '';
            for ($j = $i + 1; $j < $semicolonIndex; $j++) {
                $content .= $tokens[$j]->getContent();
            }
            $content = trim($content);

            if ($content === 'OpenApi\Spec as OA') {
                $hasAlias = true;
            } elseif ($content === 'OpenApi\Spec') {
                $namespaceImport = ['start' => $i, 'end' => $semicolonIndex];
            } elseif (str_starts_with($content, 'OpenApi\Spec\\')) {
                $relative = substr($content, strlen('OpenApi\Spec\\'));
                $shortName = str_contains($relative, '\\')
                    ? substr($relative, strrpos($relative, '\\') + 1)
                    : $relative;
                $individualImports[] = [
                    'start' => $i,
                    'end' => $semicolonIndex,
                    'relative' => $relative,
                    'shortName' => $shortName,
                ];
            }
        }

        if ($hasAlias && !$individualImports) {
            return;
        }
        if (!$hasAlias && $namespaceImport === null && !$individualImports) {
            return;
        }

        // Step 1: Rewrite short name references for individual imports → OA\relative\path
        // Do this BEFORE modifying use statements so token positions are stable for name lookups
        foreach ($individualImports as $import) {
            $this->rewriteShortName($tokens, $import['shortName'], 'OA\\' . $import['relative']);
        }

        // Step 2: Rewrite bare `Spec\` references → `OA\` (from namespace import)
        if ($namespaceImport !== null) {
            $count = $tokens->count();
            for ($i = 0; $i < $count; $i++) {
                if (!$tokens[$i]->isGivenKind(T_STRING) || $tokens[$i]->getContent() !== 'Spec') {
                    continue;
                }
                $prev = $tokens->getPrevMeaningfulToken($i);
                if ($prev !== null && $tokens[$prev]->isGivenKind(T_NS_SEPARATOR)) {
                    continue;
                }
                // Skip namespace/use declarations
                $lineStart = $this->findLineStart($tokens, $i);
                if ($lineStart !== null && ($tokens[$lineStart]->isGivenKind(T_USE) || $tokens[$lineStart]->isGivenKind(T_NAMESPACE))) {
                    continue;
                }
                $next = $tokens->getNextMeaningfulToken($i);
                if ($next !== null && $tokens[$next]->isGivenKind(T_NS_SEPARATOR)) {
                    $tokens[$i] = new Token([T_STRING, 'OA']);
                }
            }
        }

        // Step 3: Remove individual import lines (reverse order to keep indices valid)
        foreach (array_reverse($individualImports) as $import) {
            $this->clearUseLine($tokens, $import['start'], $import['end']);
        }

        // Step 4: Replace or insert the alias
        if (!$hasAlias) {
            if ($namespaceImport !== null) {
                $this->clearUseLine($tokens, $namespaceImport['start'], $namespaceImport['end']);
                $tokens->insertAt($namespaceImport['start'], [
                    new Token([T_USE, 'use']),
                    new Token([T_WHITESPACE, ' ']),
                    new Token([T_STRING, 'OpenApi']),
                    new Token([T_NS_SEPARATOR, '\\']),
                    new Token([T_STRING, 'Spec']),
                    new Token([T_WHITESPACE, ' ']),
                    new Token([T_AS, 'as']),
                    new Token([T_WHITESPACE, ' ']),
                    new Token([T_STRING, 'OA']),
                    new Token(';'),
                    new Token([T_WHITESPACE, "\n"]),
                ]);
            } else {
                // Find position of first removed import to insert alias there
                $insertAt = $individualImports[count($individualImports) - 1]['start'];
                $tokens->insertAt($insertAt, [
                    new Token([T_USE, 'use']),
                    new Token([T_WHITESPACE, ' ']),
                    new Token([T_STRING, 'OpenApi']),
                    new Token([T_NS_SEPARATOR, '\\']),
                    new Token([T_STRING, 'Spec']),
                    new Token([T_WHITESPACE, ' ']),
                    new Token([T_AS, 'as']),
                    new Token([T_WHITESPACE, ' ']),
                    new Token([T_STRING, 'OA']),
                    new Token(';'),
                    new Token([T_WHITESPACE, "\n"]),
                ]);
            }
        }
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'OpenApi\Spec must be imported with the OA alias: `use OpenApi\Spec as OA;`',
            []
        );
    }

    public function getName(): string
    {
        return 'OpenApi/spec_namespace_alias';
    }

    public function getPriority(): int
    {
        return 0;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        if (!parent::isCandidate($tokens)) {
            return false;
        }

        // Skip files in the OpenApi\Spec namespace itself (they can't alias their own namespace)
        for ($i = 0, $count = $tokens->count(); $i < $count; $i++) {
            if (!$tokens[$i]->isGivenKind(T_NAMESPACE)) {
                continue;
            }

            $semicolonIndex = $tokens->getNextTokenOfKind($i, [';', '{']);
            if ($semicolonIndex === null) {
                return true;
            }

            $content = '';
            for ($j = $i + 1; $j < $semicolonIndex; $j++) {
                $content .= $tokens[$j]->getContent();
            }

            return trim($content) !== 'OpenApi\Spec';
        }

        return true;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    protected function clearUseLine(Tokens $tokens, int $start, int $end): void
    {
        $clearEnd = $end;
        if (isset($tokens[$clearEnd + 1]) && $tokens[$clearEnd + 1]->isGivenKind(T_WHITESPACE)) {
            $clearEnd++;
        }
        $tokens->clearRange($start, $clearEnd);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    protected function rewriteShortName(Tokens $tokens, string $shortName, string $qualifiedPath): void
    {
        $count = $tokens->count();
        for ($i = 0; $i < $count; $i++) {
            if (!$tokens[$i]->isGivenKind(T_STRING) || $tokens[$i]->getContent() !== $shortName) {
                continue;
            }

            // Skip if part of a qualified name (preceded by \)
            $prev = $tokens->getPrevMeaningfulToken($i);
            if ($prev !== null && $tokens[$prev]->isGivenKind(T_NS_SEPARATOR)) {
                continue;
            }

            // Skip namespace and use statements
            $lineStart = $this->findLineStart($tokens, $i);
            if ($lineStart !== null && ($tokens[$lineStart]->isGivenKind(T_USE) || $tokens[$lineStart]->isGivenKind(T_NAMESPACE))) {
                continue;
            }

            $next = $tokens->getNextMeaningfulToken($i);
            if ($next === null) {
                continue;
            }

            $isClassUsage = $tokens[$next]->equals('(')
                || $tokens[$next]->isGivenKind(T_DOUBLE_COLON)
                || $tokens[$next]->isGivenKind(T_DOUBLE_ARROW)
                || $tokens[$next]->isGivenKind(T_VARIABLE)
                || $tokens[$next]->equals(',')
                || $tokens[$next]->equals(')')
                || $tokens[$next]->equals(';')
                || $tokens[$next]->equals('{')
                || $tokens[$next]->equals('|')
                || $tokens[$next]->isGivenKind(T_NS_SEPARATOR)
                || $tokens[$next]->isGivenKind(T_ELLIPSIS);

            if (!$isClassUsage) {
                continue;
            }

            $parts = explode('\\', $qualifiedPath);
            $replacement = [];
            foreach ($parts as $k => $part) {
                if ($k > 0) {
                    $replacement[] = new Token([T_NS_SEPARATOR, '\\']);
                }
                $replacement[] = new Token([T_STRING, $part]);
            }

            $tokens->clearAt($i);
            $tokens->insertAt($i, $replacement);
            $count = $tokens->count();
        }
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    protected function findLineStart(Tokens $tokens, int $index): ?int
    {
        for ($i = $index - 1; $i >= 0; $i--) {
            if ($tokens[$i]->isGivenKind(T_WHITESPACE) && str_contains($tokens[$i]->getContent(), "\n")) {
                return $tokens->getNextMeaningfulToken($i);
            }
        }

        return null;
    }
}
