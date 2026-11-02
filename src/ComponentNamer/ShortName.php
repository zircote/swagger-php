<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\ComponentNamer;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\ComponentNamerInterface;
use OpenApi\Spec as OA;

/**
 * Names a component after the class or holder member it is declared on.
 *
 * - declared on a class: the class's short name, so a `$ref` given as the class resolves to it
 * - declared on a member of a class carrying `#[OA\Components]`: the member's own name,
 *   whatever kind of member it is
 * - anywhere else: null. The declaring class's name belongs to that class's own component,
 *   so the component stays inline
 */
class ShortName implements ComponentNamerInterface
{
    public function name(AttributeInterface $component): ?string
    {
        $reflector = $component->getReflector();

        if ($reflector instanceof \ReflectionClass) {
            return $this->className($reflector);
        }

        $holder = $component->getClassReflector()?->getAttributes(OA\Components::class) ?? [];
        if ($holder !== [] && ($reflector instanceof \ReflectionMethod || $reflector instanceof \ReflectionProperty || $reflector instanceof \ReflectionClassConstant || $reflector instanceof \ReflectionParameter)) {
            return $reflector->getName();
        }

        return null;
    }

    /**
     * @param \ReflectionClass<object> $class
     */
    protected function className(\ReflectionClass $class): string
    {
        return $class->getShortName();
    }
}
