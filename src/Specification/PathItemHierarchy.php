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

    public function __construct(
        protected Specification $specification,
    ) {
        foreach ($specification->pathItems as $pathItem) {
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
}
