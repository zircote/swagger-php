<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Lifts the children of a classic root `OpenApi` attribute into sibling attributes.
 *
 * Classic `OpenApi` nests the whole root -- `info:`, `servers:`, `tags:`, `externalDocs:` -- and
 * spec `OpenApi` takes `version`, `security` and `x` only. Each of the others is a root-level
 * attribute of its own in spec, stacked beside it on the same class, so the move is one
 * attribute per value and nothing to decide. `openapi:` becomes `version:`.
 *
 * `paths:`, `components:` and `webhooks:` are not lifted: none maps onto one root attribute,
 * and an `OpenApi` carrying any of them is left whole for the author, as is one whose children
 * are not written inline as `new` expressions.
 *
 * Works on the attribute group, since that is where siblings go, and so runs before the
 * attribute inside it is visited -- the names are still classic, and the attributes it creates
 * are renamed with everything else when the traversal reaches them.
 */
final class UnnestOpenApiRector extends AbstractRector
{
    private const OPENAPI = 'OpenApi\\Attributes\\OpenApi';

    /** Arguments that become one sibling attribute each; true where the value is a list. */
    private const LIFT = ['info' => false, 'externalDocs' => false, 'servers' => true, 'tags' => true];

    /** Arguments spec `OpenApi` still takes. */
    private const KEEP = ['security', 'x', 'attachables'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Lift the children of a classic root OpenApi attribute into sibling attributes', [new CodeSample(
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\OpenApi(
    openapi: '3.0.0',
    info: new \OpenApi\Attributes\Info(title: 'Api', version: '1'),
    tags: [new \OpenApi\Attributes\Tag(name: 'public')],
)]
class Api
{
}
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\OpenApi(version: '3.0.0'), \OpenApi\Attributes\Info(title: 'Api', version: '1'), \OpenApi\Attributes\Tag(name: 'public')]
class Api
{
}
CODE_SAMPLE
        )]);
    }

    public function getNodeTypes(): array
    {
        return [AttributeGroup::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof AttributeGroup) {
            return null;
        }

        $attrs = [];
        $changed = false;
        foreach ($node->attrs as $attr) {
            $attrs[] = $attr;

            // an empty list still counts: `openapi:` was renamed even if nothing was lifted
            $lifted = $this->isName($attr->name, self::OPENAPI) ? $this->lift($attr) : null;
            if (null !== $lifted) {
                array_push($attrs, ...$lifted);
                $changed = true;
            }
        }

        if (!$changed) {
            return null;
        }

        $node->attrs = $attrs;

        return $node;
    }

    /**
     * Strips the liftable arguments off `$openapi` and returns them as attributes, or returns
     * null and leaves `$openapi` untouched when there is nothing to do, or when any argument is
     * not one this rule can move.
     *
     * @return list<Attribute>|null
     */
    private function lift(Attribute $openapi): ?array
    {
        $keep = [];
        $lifted = [];
        $renamed = false;
        foreach ($openapi->args as $arg) {
            if (!$arg instanceof Arg || !$arg->name instanceof Identifier) {
                return null;
            }

            $name = $arg->name->toString();

            if ('openapi' === $name) {
                $keep[] = new Arg($arg->value, name: new Identifier('version'));
                $renamed = true;
                continue;
            }

            if (in_array($name, self::KEEP, true)) {
                $keep[] = $arg;
                continue;
            }

            if (!isset(self::LIFT[$name])) {
                return null;
            }

            $values = self::LIFT[$name] ? $this->items($arg->value) : [$arg->value];
            if (null === $values) {
                return null;
            }

            foreach ($values as $value) {
                if (!$value instanceof New_ || !$value->class instanceof Node\Name) {
                    return null;
                }

                $lifted[] = new Attribute($value->class, $value->args);
            }
        }

        if (!$lifted && !$renamed) {
            return null;
        }

        $openapi->args = $keep;

        return $lifted;
    }

    /**
     * @return list<Node\Expr>|null
     */
    private function items(Node\Expr $value): ?array
    {
        if (!$value instanceof Array_) {
            return null;
        }

        $items = [];
        foreach ($value->items as $item) {
            // a keyed or spread entry is not a plain list of attributes
            if (null === $item || null !== $item->key || $item->unpack) {
                return null;
            }

            $items[] = $item->value;
        }

        return $items;
    }
}
