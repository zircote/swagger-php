<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Turns a class-string `type:` into `ref:`.
 *
 * Classic reads `new Items(type: LogType::class)` as a reference to that class's schema and
 * emits a `$ref`. Spec takes `type` literally and emits the class name as the type, which is
 * invalid; and with nothing left referencing that class's schema, the component is pruned.
 * `ref:` says what classic inferred, and spec resolves a class-string there.
 *
 * Left alone when the attribute already has a `ref:`: the two together are the author's to
 * untangle. Matches classic and spec names alike, for the same reason as the enum rule.
 */
final class TypeClassStringToRefRector extends AbstractRector
{
    private const NAMESPACES = ['OpenApi\\Attributes\\', 'OpenApi\\Spec\\'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Turn a class-string type: into ref:', [new CodeSample(
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\Items(type: LogType::class)]
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\Items(ref: LogType::class)]
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

        $type = null;
        foreach ($node->args as $arg) {
            if (!$arg instanceof Arg) {
                continue;
            }

            if ($this->isName($arg, 'ref')) {
                return null;
            }

            if ($this->isName($arg, 'type')
                && $arg->value instanceof ClassConstFetch
                && $this->isName($arg->value->name, 'class')) {
                $type = $arg;
            }
        }

        if (null === $type) {
            return null;
        }

        $type->name = new Identifier('ref');

        return $node;
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
