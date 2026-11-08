<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Contracts\KeyResolverInterface;
use OpenApi\Contracts\RefInterface;
use OpenApi\Contracts\ResolverInterface;
use OpenApi\Resolver\Reflection;
use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentIndex;
use OpenApi\Specification\ComponentKey;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\TypedList;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Finds and resolves keys referenced by the specification that have no corresponding component.
 *
 * Two sources are inspected:
 * 1. Ref values that are raw FQCNs (not yet rewritten to `#/components/...` paths), with the
 *    qualifiers of a `Schema\Ref`
 * 2. The PHP types of a schema's documented properties that set no type of their own, and of the
 *    ones its traits add, which are plain keys
 *
 * A `KeyResolverInterface` is asked about every key. A resolver implementing `ResolverInterface`
 * alone is asked about plain keys only: it knows the class alone, and would describe the plain
 * class for a key that means another description of it.
 */
class Resolver implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected const MAX_ITERATIONS = 50;

    /**
     * @param TypedList<ResolverInterface>|null $resolvers defaults to the built-in resolvers
     */
    public function __construct(protected TypedList|null $resolvers = null)
    {
        $this->resolvers ??= new TypedList($this->getDefaultResolvers());
    }

    /**
     * @param TypedList<ResolverInterface> $resolvers
     */
    public function setResolvers(TypedList $resolvers): static
    {
        $this->resolvers = $resolvers;

        return $this;
    }

    /**
     * Configure the resolvers via callable.
     *
     * @param callable(TypedList<ResolverInterface>): (TypedList<ResolverInterface>|void) $hook
     */
    public function withResolvers(callable $hook): static
    {
        $hook($this->resolvers);

        return $this;
    }

    /**
     * Resolve all found unresolved keys in the specification assembled so far.
     *
     * The first resolver to claim success will resolve the key. A resolver implementing
     * `LoggerAwareInterface` is given this resolver's logger first. One implementing
     * `ResolverInterface` alone is reported as deprecated, once per run.
     */
    public function resolve(Assembler $assembler): void
    {
        if ($this->resolvers->count() === 0) {
            return;
        }

        foreach ($this->resolvers as $resolver) {
            if (!$resolver instanceof KeyResolverInterface) {
                trigger_deprecation('zircote/swagger-php', '7.2', '%s implements `ResolverInterface` alone, which is deprecated and will be removed in 8.0; implement `KeyResolverInterface`', $resolver::class);
            }

            if ($resolver instanceof LoggerAwareInterface && $this->logger instanceof LoggerInterface) {
                $resolver->setLogger($this->logger);
            }
        }

        $specification = $assembler->getSpecification();

        // keys no resolver was able to handle; not worth another attempt in this run
        $attempted = [];

        $iterations = 0;
        do {
            if (++$iterations > static::MAX_ITERATIONS) {
                throw new OpenApiException(sprintf('Resolver loop did not converge after %d iterations; check for resolvers that return true without resolving', static::MAX_ITERATIONS));
            }

            $unresolved = $this->findUnresolved($specification, $assembler->getAttributeFactory());
            $resolved = false;
            foreach ($unresolved as $id => $key) {
                if (isset($attempted[$id])) {
                    continue;
                }

                foreach ($this->resolvers as $resolver) {
                    if (!$resolver instanceof KeyResolverInterface && !$key->isPlain()) {
                        continue;
                    }

                    try {
                        $claimed = $resolver instanceof KeyResolverInterface
                            ? $resolver->resolveKey($key, $assembler)
                            : $resolver->resolve($key->class, $assembler);
                    } catch (\Throwable $throwable) {
                        // as the scan does for a class it cannot load: report it, and resolve the rest
                        $this->logger?->error("Class \"{$id}\" cannot be loaded, skipped: {$throwable->getMessage()}");
                        $attempted[$id] = true;

                        continue 2;
                    }

                    if ($claimed) {
                        $resolved = true;
                        continue 2;
                    }
                }

                $attempted[$id] = true;
            }
        } while ($resolved);
    }

    /**
     * @return list<ResolverInterface>
     */
    protected function getDefaultResolvers(): array
    {
        return [new Reflection()];
    }

    /**
     * @return array<string, ComponentKey> keys that are referenced but have no component in the
     *                                     specification, by their string
     */
    protected function findUnresolved(Specification $specification, AttributeFactory $attributeFactory): array
    {
        $index = $specification->buildComponentIndex();
        $unresolved = [];

        $this->collectFromRefs($specification, $index, $unresolved);
        $this->collectFromReflectors($specification, $index, $unresolved, $attributeFactory);

        return $unresolved;
    }

    /**
     * @param array<string, ComponentKey> $unresolved
     */
    protected function collectFromRefs(Specification $specification, ComponentIndex $index, array &$unresolved): void
    {
        $specification->getWalker()->eachRef(function (RefInterface $attribute) use ($index, &$unresolved): void {
            $key = $attribute->getRefKey();
            if (!$key instanceof ComponentKey || !class_exists($key->class)) {
                return;
            }

            if (!$index->hasKey($key)) {
                $unresolved[(string) $key] = $key;
            }
        });
    }

    /**
     * The resolver runs before the augmenters, so a ref inferred from a PHP type does not exist yet.
     * Its class is collected here in advance.
     *
     * @param array<string, ComponentKey> $unresolved
     */
    protected function collectFromReflectors(Specification $specification, ComponentIndex $index, array &$unresolved, AttributeFactory $attributeFactory): void
    {
        foreach ($specification->schemas as $schema) {
            foreach ($this->getPropertyTypes($schema, $attributeFactory) as $type) {
                if ($type->isBuiltin()) {
                    continue;
                }

                $fqcn = $type->getName();
                if (!class_exists($fqcn) && !interface_exists($fqcn) && !enum_exists($fqcn)) {
                    continue;
                }

                $key = new ComponentKey($fqcn);
                if (!$index->hasKey($key)) {
                    $unresolved[$fqcn] = $key;
                }
            }
        }
    }

    /**
     * The PHP types of the schema's properties that leave their type to the PHP declaration.
     *
     * Only a property attribute on a PHP property or promoted parameter counts, and only where its
     * schema says nothing about the type yet. A public property without one is not documented, and a
     * schema built elsewhere, by a resolver from another description of the class, has no
     * reflectors on its properties.
     *
     * The properties of the class's traits count too: `Augmenter\Inheritance\Schemas` merges them
     * into the schema only after the resolver has run.
     *
     * @return list<\ReflectionNamedType>
     */
    protected function getPropertyTypes(OA\Schema $schema, AttributeFactory $attributeFactory): array
    {
        $reflectors = [];
        foreach ([...$schema->properties ?? [], ...$this->getTraitProperties($schema, $attributeFactory)] as $property) {
            $reflector = $property->getReflector();
            if ((!$reflector instanceof \ReflectionProperty && !$reflector instanceof \ReflectionParameter)
                || ($property->schema instanceof OA\Schema && !$this->isUntyped($property->schema))
            ) {
                continue;
            }

            $reflectors[] = $reflector;
        }

        // in the order the class declares them, which is the order their components are added in
        $declared = array_flip(array_map(
            static fn (\ReflectionProperty $property): string => $property->getName(),
            $schema->getClassReflector()?->getProperties() ?? [],
        ));
        usort($reflectors, static fn (\Reflector $a, \Reflector $b): int => ($declared[$a->getName()] ?? PHP_INT_MAX) <=> ($declared[$b->getName()] ?? PHP_INT_MAX));

        $types = [];
        foreach ($reflectors as $reflector) {
            foreach ($this->extractNamedTypes($reflector->getType()) as $type) {
                $types[] = $type;
            }
        }

        return $types;
    }

    /**
     * The documented properties `Augmenter\Inheritance\Schemas` merges into the schema from the
     * class's own traits, and none for a schema it leaves alone.
     *
     * @return list<OA\Property>
     */
    protected function getTraitProperties(OA\Schema $schema, AttributeFactory $attributeFactory): array
    {
        $reflector = $schema->getClassReflector();
        if (!$reflector instanceof \ReflectionClass || $schema->getMeta(Augmenter\Inheritance\Schemas::class) === false) {
            return [];
        }

        $properties = [];
        foreach ($attributeFactory->getDirectTraits($reflector) as $trait) {
            foreach ($attributeFactory->membersOf($trait) as $member) {
                if ($member instanceof OA\Property) {
                    $properties[] = $member;
                }
            }
        }

        return $properties;
    }

    /**
     * Whether the schema leaves its type to the PHP declaration.
     *
     * Mirrors the checks `Augmenter\Types` makes before it adds a `$ref`, in `augmentProperty()`,
     * `mergeIntoSchema()` and `inferSchemaType()`; a change to one belongs in the other.
     */
    protected function isUntyped(OA\Schema $schema): bool
    {
        return $schema->ref === null
            && $schema->enum === null
            && !$schema instanceof OA\Schema\Items
            && $schema->type === null
            && $schema->oneOf === null
            && $schema->allOf === null
            && $schema->anyOf === null
            && $schema->items === null
            && !$schema->properties
            && !$schema->patternProperties;
    }

    /**
     * @return list<\ReflectionNamedType>
     */
    protected function extractNamedTypes(?\ReflectionType $type): array
    {
        if ($type instanceof \ReflectionNamedType) {
            return [$type];
        }

        if ($type instanceof \ReflectionUnionType || $type instanceof \ReflectionIntersectionType) {
            $named = [];
            foreach ($type->getTypes() as $inner) {
                if ($inner instanceof \ReflectionNamedType) {
                    $named[] = $inner;
                }
            }

            return $named;
        }

        return [];
    }
}
