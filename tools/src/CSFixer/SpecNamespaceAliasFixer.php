<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\CSFixer;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Enforces `use OpenApi\Spec as OA;` and rewrites individual Spec class imports to use the OA\ alias.
 */
class SpecNamespaceAliasFixer extends AbstractFixer
{
    use ScopedTrait;

    protected const SPEC_NAMESPACE = 'OpenApi\Spec';

    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(\SplFileInfo $file, Tokens $tokens): void
    {
        $useAnalyzer = new NamespaceUsesAnalyzer();
        $declarations = $useAnalyzer->getDeclarationsFromTokens($tokens);

        $hasAlias = false;
        $namespaceImport = null;
        $individualImports = [];

        foreach ($declarations as $declaration) {
            if (!$declaration->isClass()) {
                continue;
            }

            $fullName = $declaration->getFullName();

            if ($fullName === static::SPEC_NAMESPACE && $declaration->isAliased() && $declaration->getShortName() === 'OA') {
                $hasAlias = true;
            } elseif ($fullName === static::SPEC_NAMESPACE && !$declaration->isAliased()) {
                $namespaceImport = $declaration;
            } elseif (str_starts_with($fullName, static::SPEC_NAMESPACE . '\\')) {
                $individualImports[] = $declaration;
            }
        }

        if ($hasAlias && !$individualImports) {
            return;
        }
        if (!$hasAlias && $namespaceImport === null && !$individualImports) {
            return;
        }

        $useStatementIndices = $this->collectUseStatementIndices($declarations);

        // Rewrite short name references for individual imports → OA\relative\path
        foreach ($individualImports as $import) {
            $relative = substr($import->getFullName(), strlen(static::SPEC_NAMESPACE) + 1);
            $this->rewriteReferences($tokens, $import->getShortName(), 'OA\\' . $relative, $useStatementIndices);
        }

        // Rewrite bare `Spec\` references → `OA\` (from namespace import)
        if ($namespaceImport !== null) {
            $this->rewriteSpecPrefix($tokens, $useStatementIndices);
        }

        // Remove individual import lines (reverse order to keep indices valid)
        $sortedImports = $individualImports;
        usort($sortedImports, fn ($aa, $bb) => $bb->getStartIndex() - $aa->getStartIndex());
        foreach ($sortedImports as $import) {
            $this->removeUseStatement($tokens, $import);
        }

        // Replace or insert the alias
        if (!$hasAlias) {
            if ($namespaceImport !== null) {
                $insertAt = $namespaceImport->getStartIndex();
                $this->removeUseStatement($tokens, $namespaceImport);
                $this->insertAliasImport($tokens, $insertAt);
            } else {
                $lastImport = $sortedImports[0];
                $this->insertAliasImport($tokens, $lastImport->getStartIndex());
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
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        if (!parent::isCandidate($tokens)) {
            return false;
        }

        if (!$tokens->isTokenKindFound(T_USE)) {
            return false;
        }

        foreach ($tokens->getNamespaceDeclarations() as $namespace) {
            if ($namespace->getFullName() === static::SPEC_NAMESPACE || str_starts_with($namespace->getFullName(), static::SPEC_NAMESPACE . '\\')) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Tokens<Token>   $tokens
     * @param array<int, int> $useStatementIndices
     */
    protected function rewriteReferences(Tokens $tokens, string $shortName, string $qualifiedPath, array $useStatementIndices): void
    {
        $count = $tokens->count();
        for ($ii = 0; $ii < $count; $ii++) {
            if (isset($useStatementIndices[$ii])) {
                $ii = $useStatementIndices[$ii];
                continue;
            }

            if (!$tokens[$ii]->isGivenKind(T_STRING) || $tokens[$ii]->getContent() !== $shortName) {
                continue;
            }

            $prev = $tokens->getPrevMeaningfulToken($ii);
            if ($prev !== null && $tokens[$prev]->isGivenKind([T_NS_SEPARATOR, T_DOUBLE_COLON, T_FUNCTION, T_NAMESPACE])) {
                continue;
            }
            if ($prev !== null && $tokens[$prev]->isObjectOperator()) {
                continue;
            }

            $next = $tokens->getNextMeaningfulToken($ii);
            if ($next === null) {
                continue;
            }

            if (!$this->isClassUsageContext($tokens, $next)) {
                continue;
            }

            $parts = explode('\\', $qualifiedPath);
            $replacement = [];
            foreach ($parts as $kk => $part) {
                if ($kk > 0) {
                    $replacement[] = new Token([T_NS_SEPARATOR, '\\']);
                }
                $replacement[] = new Token([T_STRING, $part]);
            }

            $tokens->clearAt($ii);
            $tokens->insertAt($ii, $replacement);
            $count = $tokens->count();
        }
    }

    /**
     * @param Tokens<Token>   $tokens
     * @param array<int, int> $useStatementIndices
     */
    protected function rewriteSpecPrefix(Tokens $tokens, array $useStatementIndices): void
    {
        $count = $tokens->count();
        for ($ii = 0; $ii < $count; $ii++) {
            if (isset($useStatementIndices[$ii])) {
                $ii = $useStatementIndices[$ii];
                continue;
            }

            if (!$tokens[$ii]->isGivenKind(T_STRING) || $tokens[$ii]->getContent() !== 'Spec') {
                continue;
            }

            $prev = $tokens->getPrevMeaningfulToken($ii);
            if ($prev !== null && $tokens[$prev]->isGivenKind(T_NS_SEPARATOR)) {
                continue;
            }

            $next = $tokens->getNextMeaningfulToken($ii);
            if ($next !== null && $tokens[$next]->isGivenKind(T_NS_SEPARATOR)) {
                $tokens[$ii] = new Token([T_STRING, 'OA']);
            }
        }
    }

    /**
     * @param Tokens<Token> $tokens
     */
    protected function removeUseStatement(Tokens $tokens, NamespaceUseAnalysis $useAnalysis): void
    {
        $start = $useAnalysis->getStartIndex();
        $end = $useAnalysis->getEndIndex();

        $tokens->clearRange($start, $end);
        if (isset($tokens[$end + 1]) && $tokens[$end + 1]->isGivenKind(T_WHITESPACE)) {
            $whitespace = $tokens[$end + 1]->getContent();
            $newlinePos = strpos($whitespace, "\n");
            if ($newlinePos !== false) {
                $remaining = substr($whitespace, $newlinePos + 1);
                if ($remaining !== '') {
                    $tokens[$end + 1] = new Token([T_WHITESPACE, $remaining]);
                } else {
                    $tokens->clearAt($end + 1);
                }
            }
        }
    }

    /**
     * @param Tokens<Token> $tokens
     */
    protected function insertAliasImport(Tokens $tokens, int $insertAt): void
    {
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

    /**
     * @param Tokens<Token> $tokens
     */
    protected function isClassUsageContext(Tokens $tokens, int $nextIndex): bool
    {
        return $tokens[$nextIndex]->equals('(')
            || $tokens[$nextIndex]->isGivenKind(T_DOUBLE_COLON)
            || $tokens[$nextIndex]->isGivenKind(T_VARIABLE)
            || $tokens[$nextIndex]->equals(',')
            || $tokens[$nextIndex]->equals(')')
            || $tokens[$nextIndex]->equals(';')
            || $tokens[$nextIndex]->equals('{')
            || $tokens[$nextIndex]->equals('|')
            || $tokens[$nextIndex]->isGivenKind(T_NS_SEPARATOR)
            || $tokens[$nextIndex]->isGivenKind(T_ELLIPSIS);
    }

    /**
     * @param  list<NamespaceUseAnalysis> $declarations
     * @return array<int, int>
     */
    protected function collectUseStatementIndices(array $declarations): array
    {
        $indices = [];
        foreach ($declarations as $declaration) {
            $indices[$declaration->getStartIndex()] = $declaration->getEndIndex();
        }

        return $indices;
    }
}
