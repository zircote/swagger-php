<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\ResolverInterface;
use OpenApi\Resolver\Reflection;
use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentIndex;
use OpenApi\Utils\TypedList;

/**
 * Finds and resolves FQCNs referenced by the specification that have no corresponding schema.
 *
 * Two sources are inspected:
 * 1. Ref values that are raw FQCNs (not yet rewritten to `#/components/...` paths)
 * 2. Property/parameter type hints on schema class reflectors
 */
class Resolver
{
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
     * Resolve all found unresolved FQCN in the specification assembled so far.
     *
     * The first resolver to claim success will resolve the FQCN.
     */
    public function resolve(Assembler $assembler): void
    {
        if ($this->resolvers->count() === 0) {
            return;
        }

        $specification = $assembler->getSpecification();

        // FQCN no resolver was able to handle; not worth another attempt in this run
        $attempted = [];

        $iterations = 0;
        do {
            if (++$iterations > static::MAX_ITERATIONS) {
                throw new OpenApiException(sprintf('Resolver loop did not converge after %d iterations; check for resolvers that return true without resolving', static::MAX_ITERATIONS));
            }

            $unresolved = $this->findUnresolved($specification);
            $resolved = false;
            foreach ($unresolved as $fqcn) {
                if (isset($attempted[$fqcn])) {
                    continue;
                }

                foreach ($this->resolvers as $resolver) {
                    if ($resolver->resolve($fqcn, $assembler)) {
                        $resolved = true;
                        continue 2;
                    }
                }

                $attempted[$fqcn] = true;
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
     * @return list<string> FQCNs that are referenced but have no schema in the specification
     */
    protected function findUnresolved(Specification $specification): array
    {
        $index = $specification->buildComponentIndex();
        $unresolved = [];

        $this->collectFromRefs($specification, $index, $unresolved);
        $this->collectFromReflectors($specification, $index, $unresolved);

        return array_values(array_unique($unresolved));
    }

    protected function collectFromRefs(Specification $specification, ComponentIndex $index, array &$unresolved): void
    {
        $specification->getWalker()->eachRef(function (AttributeInterface $attribute) use ($index, &$unresolved): void {
            if (!property_exists($attribute, 'ref') || $attribute->ref === null) {
                return;
            }

            $ref = $attribute->ref instanceof OA\Schema\Ref
                ? $attribute->ref->ref
                : $attribute->ref;

            if (str_starts_with($ref, '#/')) {
                return;
            }

            if (!class_exists($ref)) {
                return;
            }

            if (!$index->find($ref) instanceof AttributeInterface) {
                $unresolved[] = $ref;
            }
        });
    }

    protected function collectFromReflectors(Specification $specification, ComponentIndex $index, array &$unresolved): void
    {
        foreach ($specification->schemas as $schema) {
            $reflector = $schema->getClassReflector();
            if ($reflector === null) {
                continue;
            }

            foreach ($this->getTypedReflectors($reflector) as $type) {
                if ($type->isBuiltin()) {
                    continue;
                }

                $fqcn = $type->getName();
                if (!class_exists($fqcn) && !interface_exists($fqcn) && !enum_exists($fqcn)) {
                    continue;
                }

                if (!$index->find($fqcn) instanceof AttributeInterface) {
                    $unresolved[] = $fqcn;
                }
            }
        }
    }

    /**
     * @return list<\ReflectionNamedType>
     */
    protected function getTypedReflectors(\ReflectionClass $reflector): array
    {
        $types = [];

        foreach ($reflector->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            if ($prop->getDeclaringClass()->getName() !== $reflector->getName()) {
                continue;
            }
            foreach ($this->extractNamedTypes($prop->getType()) as $type) {
                $types[] = $type;
            }
        }

        if ($constructor = $reflector->getConstructor()) {
            foreach ($constructor->getParameters() as $param) {
                foreach ($this->extractNamedTypes($param->getType()) as $type) {
                    $types[] = $type;
                }
            }
        }

        return $types;
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
