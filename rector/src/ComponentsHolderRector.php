<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Adds `Components` to a class whose members declare keyed components.
 *
 * Classic takes a keyed component on any member. Spec takes one only in a class carrying
 * `Components`, and drops it with a warning otherwise, so a migrated class would lose them.
 *
 * An attribute counts when it is keyed and nothing beside it on the same member takes it in: a
 * response beside an operation, or a link beside a response, is nested, not a component. A class
 * with a `Schema` of its own is left alone. It is a model, and in a holder its property schemas
 * would turn into components.
 *
 * A member carrying one of the configured route attributes is left out: where a framework derives
 * the operation from the route, a keyed response beside it decorates that operation. None are
 * configured by default.
 */
final class ComponentsHolderRector extends AbstractRector implements ConfigurableRectorInterface
{
    public const ROUTE_ATTRIBUTES = 'routeAttributes';

    private const NAMESPACES = ['OpenApi\\Attributes\\', 'OpenApi\\Spec\\'];

    /** Each component kind with its classic key, and the kinds that take it in beside it. */
    private const KINDS = [
        'schema' => ['schema', ['property', 'parameter', 'header', 'mediaType']],
        'parameter' => ['parameter', ['operation']],
        'requestBody' => ['request', ['operation']],
        'securityScheme' => ['securityScheme', []],
        'response' => ['response', ['operation']],
        'header' => ['header', ['response', 'encoding']],
        'link' => ['link', ['response']],
        'example' => ['example', ['parameter', 'header', 'mediaType']],
    ];

    private const OPERATIONS = ['Get', 'Post', 'Put', 'Patch', 'Delete', 'Head', 'Options', 'Trace', 'Webhook', 'PathItem'];

    /**
     * @var list<string>
     */
    private array $routeAttributes = [];

    /**
     * @param array{routeAttributes?: list<class-string>} $configuration
     */
    public function configure(array $configuration): void
    {
        $this->routeAttributes = array_map(
            static fn (string $class): string => ltrim($class, '\\'),
            array_values($configuration[self::ROUTE_ATTRIBUTES] ?? []),
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add Components to a class whose members declare keyed components', [new CodeSample(
            <<<'CODE_SAMPLE'
class Models
{
    #[\OpenApi\Attributes\Schema(schema: 'Thing', type: 'object')]
    public function thing(): void
    {
    }
}
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\Components]
class Models
{
    #[\OpenApi\Attributes\Schema(schema: 'Thing', type: 'object')]
    public function thing(): void
    {
    }
}
CODE_SAMPLE
        ), new ConfiguredCodeSample(
            <<<'CODE_SAMPLE'
class PetController
{
    #[\Symfony\Component\Routing\Attribute\Route('/pets', methods: ['GET'])]
    #[\OpenApi\Attributes\Response(response: 200, description: 'The pets')]
    public function list(): void
    {
    }
}
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
class PetController
{
    #[\Symfony\Component\Routing\Attribute\Route('/pets', methods: ['GET'])]
    #[\OpenApi\Attributes\Response(response: 200, description: 'The pets')]
    public function list(): void
    {
    }
}
CODE_SAMPLE,
            [self::ROUTE_ATTRIBUTES => ['Symfony\Component\Routing\Attribute\Route']]
        )]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        foreach ($this->attributes($node->attrGroups) as [, $local]) {
            if ($local === 'Components' || $local === 'Schema') {
                return null;
            }
        }

        $namespace = null;
        foreach ($this->memberAttributeGroups($node) as $groups) {
            if ($this->isRoute($groups)) {
                continue;
            }

            $attributes = $this->attributes($groups);
            $kinds = array_map(fn (array $attribute): ?string => $this->kind($attribute[1]), $attributes);

            foreach ($attributes as $index => [$attribute, $local, $prefix]) {
                $kind = $kinds[$index];
                if ($kind === null || !isset(self::KINDS[$kind]) || !$this->isKeyed($attribute, self::KINDS[$kind][0])) {
                    continue;
                }

                if (array_intersect(self::KINDS[$kind][1], $kinds) === []) {
                    $namespace = $prefix;
                    break 2;
                }
            }
        }

        if ($namespace === null) {
            return null;
        }

        $node->attrGroups[] = new AttributeGroup([new Attribute(new FullyQualified($namespace . 'Components'))]);

        return $node;
    }

    /**
     * The attribute groups of each member: a property, a constant, a method, or a promoted
     * constructor parameter.
     *
     * @return list<list<AttributeGroup>>
     */
    private function memberAttributeGroups(Class_ $class): array
    {
        $members = [];
        foreach ($class->stmts as $stmt) {
            if ($stmt instanceof Property || $stmt instanceof ClassConst) {
                $members[] = $stmt->attrGroups;
            } elseif ($stmt instanceof ClassMethod) {
                if (!$this->isName($stmt, '__construct')) {
                    $members[] = $stmt->attrGroups;
                    continue;
                }

                foreach ($stmt->params as $param) {
                    if ($param->flags !== 0) {
                        $members[] = $param->attrGroups;
                    }
                }
            }
        }

        return $members;
    }

    /**
     * Each OpenApi attribute with its class name below the namespace, and the namespace.
     *
     * @param list<AttributeGroup> $groups
     *
     * @return list<array{Attribute, string, string}>
     */
    private function attributes(array $groups): array
    {
        $attributes = [];
        foreach ($groups as $group) {
            foreach ($group->attrs as $attribute) {
                $name = $this->getName($attribute->name);
                foreach (self::NAMESPACES as $namespace) {
                    if ($name !== null && str_starts_with($name, $namespace)) {
                        $attributes[] = [$attribute, substr($name, strlen($namespace)), $namespace];
                    }
                }
            }
        }

        return $attributes;
    }

    /**
     * The kind of an attribute, from its class name below the namespace.
     */
    private function kind(string $local): ?string
    {
        return match (true) {
            in_array($local, self::OPERATIONS, true), str_starts_with($local, 'Operation\\') => 'operation',
            $local === 'Schema' => 'schema',
            $local === 'Parameter', str_ends_with($local, 'Parameter'), str_starts_with($local, 'Parameter\\') => 'parameter',
            $local === 'RequestBody' => 'requestBody',
            $local === 'SecurityScheme', $local === 'Security\\Scheme', str_starts_with($local, 'Security\\Scheme\\') => 'securityScheme',
            $local === 'Response' => 'response',
            $local === 'Header' => 'header',
            $local === 'Link' => 'link',
            $local === 'Example' => 'example',
            $local === 'Encoding' => 'encoding',
            $local === 'Property', str_starts_with($local, 'Property\\') => 'property',
            $local === 'MediaType', str_starts_with($local, 'MediaType\\'), $local === 'JsonContent', $local === 'XmlContent' => 'mediaType',
            default => null,
        };
    }

    /**
     * Whether a member carries one of the configured route attributes.
     *
     * @param list<AttributeGroup> $groups
     */
    private function isRoute(array $groups): bool
    {
        foreach ($groups as $group) {
            foreach ($group->attrs as $attribute) {
                if (in_array($this->getName($attribute->name), $this->routeAttributes, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isKeyed(Attribute $attribute, string $key): bool
    {
        foreach ($attribute->args as $arg) {
            if ($arg instanceof Arg && ($this->isName($arg, 'component') || $this->isName($arg, $key))) {
                return true;
            }
        }

        return false;
    }
}
