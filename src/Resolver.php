<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Contracts\KeyResolverInterface;
use OpenApi\Contracts\RefInterface;
use OpenApi\Contracts\ResolverInterface;
use OpenApi\Resolver\Reflection;
use OpenApi\Specification\ComponentIndex;
use OpenApi\Specification\ComponentKey;
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
 * 2. Property/parameter type hints on schema class reflectors, which are plain keys
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

            $unresolved = $this->findUnresolved($specification);
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
                        $this->logger?->warning("Skipping unloadable {$id}: {$throwable->getMessage()}");
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
    protected function findUnresolved(Specification $specification): array
    {
        $index = $specification->buildComponentIndex();
        $unresolved = [];

        $this->collectFromRefs($specification, $index, $unresolved);
        $this->collectFromReflectors($specification, $index, $unresolved);

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
     * @param array<string, ComponentKey> $unresolved
     */
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

                $key = new ComponentKey($fqcn);
                if (!$index->hasKey($key)) {
                    $unresolved[$fqcn] = $key;
                }
            }
        }
    }

    /**
     * @param \ReflectionClass<object> $reflector
     *
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

        if (($constructor = $reflector->getConstructor()) instanceof \ReflectionMethod) {
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
