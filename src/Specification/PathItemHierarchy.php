<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Specification;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\ClassReflector;

/**
 * The PathItems governing a class, and the order they apply in.
 *
 * A PathItem sits on a class; a class without one of its own is governed by its ancestors',
 * which is how a base controller's prefix, tags and security reach a subclass's operations.
 * Anything read off a PathItem without walking that chain applies to nothing at all, and says
 * nothing while it does — so the walk lives here rather than in each caller.
 *
 * Parent classes only: traits and interfaces are not followed. `Augmenter\Inheritance\Schemas`
 * does follow them when composing `allOf`, and the difference is deliberate — a prefix chain
 * is ordered, and a trait or interface graph offers no order to compose in.
 *
 * An instance is a snapshot of the specification's path items, taken when it is built. A
 * caller that adds or removes path items mid-pass builds another one.
 */
class PathItemHierarchy
{
    /** @var array<class-string, OA\PathItem> */
    protected array $classToPathItem = [];

    /** @var array<string, list<OA\PathItem>> */
    protected array $chains = [];

    protected ?ComponentIndex $componentIndex = null;

    public function __construct(
        protected Specification $specification,
    ) {
        foreach ($specification->pathItems as $pathItem) {
            if ($pathItem->component !== null) {
                continue; // a reusable path item, filed under components; it governs no class
            }

            $className = $pathItem->getClassName();
            if ($className !== null) {
                $this->classToPathItem[$className] = $pathItem;
            }
        }
    }

    /**
     * Every class carrying a PathItem of its own.
     *
     * @return array<class-string, OA\PathItem>
     */
    public function classes(): array
    {
        return $this->classToPathItem;
    }

    /**
     * The PathItems governing a class, outermost ancestor first, including the class's own.
     *
     * @return list<OA\PathItem>
     */
    public function chain(?string $className): array
    {
        if ($className === null) {
            return [];
        }

        if (isset($this->chains[$className])) {
            return $this->chains[$className];
        }

        // not `new \ReflectionClass()` in a try/catch: a class-string is a name, not a promise
        // the class loads (see ClassReflector), and phpstan reads the catch as dead
        [$reflector] = ClassReflector::tryReflect($className);

        return $this->chains[$className] = $reflector instanceof \ReflectionClass
            ? $this->chainFor($reflector)
            : [];
    }

    /**
     * The same chain, for a caller that already holds the reflector.
     *
     * @param  \ReflectionClass<object> $reflector
     * @return list<OA\PathItem>
     */
    public function chainFor(\ReflectionClass $reflector): array
    {
        $className = $reflector->getName();
        if (isset($this->chains[$className])) {
            return $this->chains[$className];
        }

        $chain = [];
        $current = $reflector;
        while ($current !== false) {
            $pathItem = $this->classToPathItem[$current->getName()] ?? null;
            if ($pathItem !== null) {
                $chain[] = $pathItem;
            }
            $current = $current->getParentClass();
        }

        return $this->chains[$className] = array_reverse($chain);
    }

    /**
     * The nearest governing PathItem — the class's own, or the closest ancestor's.
     */
    public function governing(?string $className): ?OA\PathItem
    {
        $chain = $this->chain($className);

        return $chain === [] ? null : $chain[count($chain) - 1];
    }

    /**
     * The chain governing the class an operation is declared in.
     *
     * @return list<OA\PathItem>
     */
    public function forOperation(OA\Operation $operation): array
    {
        $reflector = $operation->getClassReflector();

        return $reflector instanceof \ReflectionClass ? $this->chainFor($reflector) : [];
    }

    /**
     * The path prefix the chain governing an operation's class composes to, `''` for none.
     *
     * Each prefix is trimmed of slashes and the rest are joined outermost first, so `/api/v1`
     * and `/users/` compose to `/api/v1/users`.
     */
    public function prefixFor(OA\Operation $operation): string
    {
        $parts = [];
        foreach ($this->forOperation($operation) as $pathItem) {
            if ($pathItem->prefix !== null && ($part = trim($pathItem->prefix, '/')) !== '') {
                $parts[] = $part;
            }
        }

        return $parts !== [] ? '/' . implode('/', $parts) : '';
    }

    /**
     * The path an operation compiles to, its own path with `prefixFor()` in front, or null when
     * it has no path.
     *
     * It reads the path as declared. `Augmenter\PathItems` writes this value back to the
     * operation, so after that augmenter has run the prefix is already there and asking again
     * adds it twice.
     */
    public function pathFor(OA\Operation $operation): ?string
    {
        if ($operation->path === null) {
            return null;
        }

        $prefix = $this->prefixFor($operation);
        if ($prefix === '') {
            return $operation->path;
        }

        $path = ltrim($operation->path, '/');

        return $path !== '' ? $prefix . '/' . $path : $prefix;
    }

    /**
     * The parameters the chain governing an operation's class declares, keyed `in:name`.
     *
     * They are emitted at path level and apply to every operation under the path. Where two
     * links of the chain declare the same parameter, the nearer one wins, as a subclass
     * overrides its parent. The key is `ComponentIndex::parameterKey()`, so a parameter that is
     * only a `ref` is keyed by its component's name and location. One with no identity at all is
     * left out, since nothing could match it.
     *
     * The operation's own parameters are not included.
     *
     * @return array<string, OA\Parameter>
     */
    public function parametersFor(OA\Operation $operation): array
    {
        $index = $this->componentIndex ??= $this->specification->buildComponentIndex();

        $parameters = [];
        foreach ($this->forOperation($operation) as $pathItem) {
            foreach ($pathItem->parameters ?? [] as $parameter) {
                $key = $index->parameterKey($parameter);
                if ($key !== null) {
                    $parameters[$key] = $parameter;
                }
            }
        }

        return $parameters;
    }
}
