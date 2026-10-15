<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Rewrites each item of `security:` into a `Security\Requirement`.
 *
 * Classic takes a requirement as a scheme name, `'bearerAuth'`, or as an array, either the map
 * `['bearerAuth' => []]` or the list `['bearerAuth']`. Spec drops a string with a warning, so
 * the operation would lose its requirement. It still compiles an array, but deprecates it for a
 * `Requirement`. A single scheme becomes `scheme:`, with `scopes:` where it has any, several
 * become `schemes:`, and an empty array becomes an empty requirement.
 *
 * Only literals are rewritten. A variable, a constant or a spread item is left as it is.
 */
final class SecurityToRequirementRector extends AbstractRector
{
    private const NAMESPACES = ['OpenApi\\Attributes\\', 'OpenApi\\Spec\\'];

    private const REQUIREMENT = 'OpenApi\\Spec\\Security\\Requirement';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Rewrite each item of security: into a Security\Requirement', [new CodeSample(
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\Get(path: '/users', security: ['bearerAuth', ['oauth2' => ['read']]])]
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\Get(path: '/users', security: [new \OpenApi\Spec\Security\Requirement(scheme: 'bearerAuth'), new \OpenApi\Spec\Security\Requirement(scheme: 'oauth2', scopes: ['read'])])]
CODE_SAMPLE
        )]);
    }

    public function getNodeTypes(): array
    {
        return [Attribute::class, New_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Attribute && !$node instanceof New_) {
            return null;
        }

        $class = $node instanceof Attribute ? $node->name : $node->class;
        if (!$class instanceof Node\Name || !$this->isOpenApiClass($this->getName($class))) {
            return null;
        }

        $changed = false;
        foreach ($node->args as $arg) {
            if (!$arg instanceof Arg || !$this->isName($arg, 'security') || !$arg->value instanceof Array_) {
                continue;
            }

            foreach ($arg->value->items as $item) {
                if ($item === null || $item->key !== null || $item->unpack) {
                    continue;
                }

                $args = $this->requirementArgs($item->value);
                if ($args !== null) {
                    $item->value = new New_(new FullyQualified(self::REQUIREMENT), $args);
                    $changed = true;
                }
            }
        }

        return $changed ? $node : null;
    }

    /**
     * The constructor arguments of the `Requirement` an item stands for, or null to leave it.
     *
     * @return list<Arg>|null
     */
    private function requirementArgs(Expr $value): ?array
    {
        if ($value instanceof String_) {
            return [$this->named('scheme', $value)];
        }

        if (!$value instanceof Array_) {
            return null;
        }

        $entries = [];
        foreach ($value->items as $entry) {
            if (!$entry instanceof ArrayItem || $entry->unpack || !$this->isLiteral($entry)) {
                return null;
            }
            $entries[] = $entry;
        }

        if ($entries === []) {
            return [];
        }

        if (count($entries) > 1) {
            return [$this->named('schemes', $value)];
        }

        $entry = $entries[0];
        if (!$entry->key instanceof Expr) {
            return [$this->named('scheme', $entry->value)];
        }

        $args = [$this->named('scheme', $entry->key)];
        if (!$entry->value instanceof Array_ || $entry->value->items !== []) {
            $args[] = $this->named('scopes', $entry->value);
        }

        return $args;
    }

    /**
     * A scheme name is a string, keyed or not, and its scopes are a list of them.
     */
    private function isLiteral(ArrayItem $entry): bool
    {
        if (!$entry->key instanceof Expr) {
            return $entry->value instanceof String_;
        }

        if (!$entry->key instanceof String_ || !$entry->value instanceof Array_) {
            return false;
        }

        foreach ($entry->value->items as $scope) {
            if (!$scope instanceof ArrayItem || $scope->key instanceof Expr || $scope->unpack || !$scope->value instanceof String_) {
                return false;
            }
        }

        return true;
    }

    private function named(string $name, Expr $value): Arg
    {
        return new Arg($value, name: new Identifier($name));
    }

    private function isOpenApiClass(?string $name): bool
    {
        foreach (self::NAMESPACES as $namespace) {
            if (null !== $name && str_starts_with($name, $namespace)) {
                return true;
            }
        }

        return false;
    }
}
