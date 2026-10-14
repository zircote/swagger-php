<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use OpenApi\Type\TypeMapper;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Rewrites a classic type alias in `type:` to the OpenAPI type it stands for.
 *
 * Classic maps `int`, `bool`, `long`, `float`, `double`, `date` and the date-time class names
 * through {@see TypeMapper::NATIVE_TYPE_MAP}. Hybrid and spec translate them too, with a
 * deprecation, until 8.0. The rule writes what the translation produces: the type, and the
 * format where the alias carries one and no `format:` is given. In a type list each name gets
 * its type alone. `mixed` and `byte` have no OpenAPI type to rewrite to and are left alone.
 *
 * The result is a plain string, valid on classic and spec attributes alike, so the rule also
 * runs on its own over code that stays on classic attributes.
 */
final class TypeAliasesRector extends AbstractRector
{
    private const NAMESPACES = ['OpenApi\\Attributes\\', 'OpenApi\\Spec\\'];

    private const UNMAPPED = ['mixed', 'byte'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Rewrite a classic type alias in type: to its OpenAPI type', [new CodeSample(
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\Property(property: 'id', type: 'long')]
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\Property(property: 'id', type: 'integer', format: 'int64')]
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
        $hasFormat = false;
        foreach ($node->args as $arg) {
            if (!$arg instanceof Arg) {
                continue;
            }

            if ($this->isName($arg, 'type')) {
                $type = $arg;
            } elseif ($this->isName($arg, 'format')) {
                $hasFormat = true;
            }
        }

        if (null === $type) {
            return null;
        }

        if ($type->value instanceof String_) {
            $mapped = $this->lookup($type->value->value);
            if (null === $mapped) {
                return null;
            }

            [$target, $format] = $mapped;
            $type->value = new String_($target);

            if (null !== $format && !$hasFormat) {
                $node->args[] = new Arg(new String_($format), name: new Identifier('format'));
            }

            return $node;
        }

        if ($type->value instanceof Array_) {
            $changed = false;
            foreach ($type->value->items as $item) {
                if (!$item instanceof ArrayItem || !$item->value instanceof String_) {
                    continue;
                }

                $mapped = $this->lookup($item->value->value);
                if (null !== $mapped) {
                    $item->value = new String_($mapped[0]);
                    $changed = true;
                }
            }

            return $changed ? $node : null;
        }

        return null;
    }

    /**
     * The OpenAPI type and format for a classic alias, or null where the name is not one.
     *
     * @return array{string, string|null}|null
     */
    private function lookup(string $type): ?array
    {
        $key = strtolower($type);

        if (in_array($key, self::UNMAPPED, true) || !array_key_exists($key, TypeMapper::NATIVE_TYPE_MAP)) {
            return null;
        }

        $mapped = TypeMapper::NATIVE_TYPE_MAP[$key];
        [$target, $format] = is_array($mapped) ? $mapped : [$mapped, null];

        return $target === $type ? null : [$target, $format];
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
