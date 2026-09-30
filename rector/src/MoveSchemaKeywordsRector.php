<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Moves schema keywords onto a nested `schema:` where the spec attribute no longer accepts them.
 *
 * Classic `Property` extends `Schema` and carries all 61 of its keywords; the spec one holds a
 * `Schema` and takes four arguments. `JsonContent` and `XmlContent` go from 60 to 11 the same
 * way. Renaming those classes alone leaves `type:`, `nullable:`, `enum:` and the rest on an
 * attribute that rejects them, which fails at generation time rather than at load -- 20 of
 * phpMyFAQ's 22 annotated files, when this was measured.
 *
 * The keywords move into `schema: new Schema(...)` rather than onto a sibling `#[OA\Schema]`
 * attribute. Both are valid spec, but the argument works identically in an attribute and in a
 * `new` expression nested inside one, and it states the nesting outright instead of leaving it
 * to the merge pass.
 *
 * What the target accepts is read by reflection, so the split follows whatever the spec
 * classes take rather than a list that goes stale beside them.
 */
final class MoveSchemaKeywordsRector extends AbstractRector
{
    /**
     * Classes that hold a schema rather than being one, keyed by the name a node may carry,
     * valued by the spec class whose signature decides what stays.
     *
     * The classic names are in here because a parent node is visited before its children, so
     * on the pass that renames `Attributes\Property` this rule sees the attribute while its
     * name is still classic. Matching only the spec names silently does nothing to code that
     * has not been renamed yet, which is to say to all of it.
     */
    private const TARGETS = [
        'OpenApi\Spec\Property' => 'OpenApi\Spec\Property',
        'OpenApi\Spec\MediaType\Json' => 'OpenApi\Spec\MediaType\Json',
        'OpenApi\Spec\MediaType\Xml' => 'OpenApi\Spec\MediaType\Xml',
        'OpenApi\Attributes\Property' => 'OpenApi\Spec\Property',
        'OpenApi\Attributes\JsonContent' => 'OpenApi\Spec\MediaType\Json',
        'OpenApi\Attributes\XmlContent' => 'OpenApi\Spec\MediaType\Xml',
    ];

    private const SCHEMA = 'OpenApi\Spec\Schema';

    /** @var array<string, list<string>> */
    private array $accepts = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Move schema keywords a spec attribute no longer accepts onto a nested schema', [new CodeSample(
            <<<'CODE_SAMPLE'
#[\OpenApi\Spec\Property(property: 'name', type: 'string', nullable: true)]
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
#[\OpenApi\Spec\Property(property: 'name', schema: new \OpenApi\Spec\Schema(type: 'string', nullable: true))]
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

        // an attribute names its class in `name`, a `new` expression in `class`
        $class = $node instanceof Attribute ? $node->name : $node->class;
        if (!$class instanceof Node\Name) {
            return null;
        }

        // resolved rather than written: by the time this runs the name may already be aliased
        // to `OAS\Property` by AliasSpecImportsRector, and comparing the written form silently
        // matches nothing
        $name = $this->getName($class);
        if (null === $name || !isset(self::TARGETS[$name])) {
            return null;
        }

        $accepted = $this->accepts(self::TARGETS[$name]);
        if (!$accepted) {
            return null;
        }

        $keep = [];
        $move = [];
        foreach ($node->args as $arg) {
            // a positional argument cannot be matched to a parameter name here; leave the whole
            // attribute alone rather than guess at its meaning
            if (!$arg instanceof Arg || !$arg->name instanceof Identifier) {
                return null;
            }

            $name = $arg->name->toString();

            // an existing schema is the author's own nesting; merging into it is a judgement
            // this rule should not make
            if ('schema' === $name) {
                return null;
            }

            if (in_array($name, $accepted, true)) {
                $keep[] = $arg;
            } else {
                $move[] = $arg;
            }
        }

        if (!$move) {
            return null;
        }

        $keep[] = new Arg(
            new New_(new FullyQualified(self::SCHEMA), $move),
            name: new Identifier('schema')
        );

        $node->args = $keep;

        return $node;
    }

    /**
     * @return list<string>
     */
    private function accepts(string $class): array
    {
        if (isset($this->accepts[$class])) {
            return $this->accepts[$class];
        }

        if (!class_exists($class)) {
            return $this->accepts[$class] = [];
        }

        $constructor = (new \ReflectionClass($class))->getConstructor();
        $names = $constructor
            ? array_map(static fn (\ReflectionParameter $p): string => $p->getName(), $constructor->getParameters())
            : [];

        return $this->accepts[$class] = $names;
    }
}
