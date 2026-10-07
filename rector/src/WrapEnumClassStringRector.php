<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\New_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Wraps a bare enum class-string in `enum:` into a list.
 *
 * Classic `enum` takes a class-string as well as a list and expands either. The spec `$enum`
 * is `?array`, so a bare `SomeEnum::class` is a `TypeError` when the attribute is instantiated.
 * A list holding the class-string expands to the cases exactly as before, so the wrap is all it
 * takes.
 *
 * Matches classic and spec names alike: the keyword may sit on an attribute that has been
 * renamed, or on a `new Schema(...)` that MoveSchemaKeywordsRector has just created.
 */
final class WrapEnumClassStringRector extends AbstractRector
{
    private const NAMESPACES = ['OpenApi\\Attributes\\', 'OpenApi\\Spec\\'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Wrap a bare enum class-string in enum: into a list', [new CodeSample(
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\Schema(enum: Suit::class)]
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\Schema(enum: [Suit::class])]
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

        foreach ($node->args as $arg) {
            if ($arg instanceof Arg
                && $this->isName($arg, 'enum')
                && $arg->value instanceof ClassConstFetch
                && $this->isName($arg->value->name, 'class')) {
                $arg->value = new Array_([new ArrayItem($arg->value)]);

                return $node;
            }
        }

        return null;
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
