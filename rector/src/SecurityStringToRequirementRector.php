<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Rewrites a bare scheme name in `security:` into a `Security\Requirement`.
 *
 * Classic reads `security: ['bearerAuth']` as `bearerAuth` with no scopes. Spec takes a
 * `Security\Requirement` per entry and drops a string with a warning, so the operation would
 * lose its requirement. A string there can only be a scheme name, so the rewrite is exact.
 *
 * Only the strings are rewritten. A requirement written as an array is left as it is.
 */
final class SecurityStringToRequirementRector extends AbstractRector
{
    private const NAMESPACES = ['OpenApi\\Attributes\\', 'OpenApi\\Spec\\'];

    private const REQUIREMENT = 'OpenApi\\Spec\\Security\\Requirement';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Rewrite a bare scheme name in security: into a Security\Requirement', [new CodeSample(
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\Get(path: '/users', security: ['bearerAuth'])]
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\Get(path: '/users', security: [new \OpenApi\Spec\Security\Requirement(scheme: 'bearerAuth')])]
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
                if ($item !== null && $item->key === null && $item->value instanceof String_) {
                    $item->value = new New_(
                        new FullyQualified(self::REQUIREMENT),
                        [new Arg($item->value, name: new Identifier('scheme'))],
                    );
                    $changed = true;
                }
            }
        }

        return $changed ? $node : null;
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
