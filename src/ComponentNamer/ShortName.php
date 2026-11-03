<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\ComponentNamer;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\ComponentNamerInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentKey;

/**
 * Names a component after the class or holder member it is declared on.
 *
 * - declared on a class: the class's short name, so a `$ref` given as the class resolves to it
 * - declared on a member of a class carrying `#[OA\Components]`: the member's own name,
 *   whatever kind of member it is
 * - anywhere else: null. The declaring class's name belongs to that class's own component,
 *   so the component stays inline
 *
 * A component whose key has qualifiers adds them, so each description of a class gets a name of
 * its own: `-` before each qualifier, `.` between its name and its values, `_` between values.
 * `Author` with groups `admin` and `summary` is `Author-groups.admin_summary`.
 */
class ShortName implements ComponentNamerInterface
{
    public function name(AttributeInterface $component): ?string
    {
        $name = $this->baseName($component);
        $key = $component->getComponentKey();

        return $name !== null && $key instanceof ComponentKey && !$key->isPlain()
            ? $name . $this->qualifiers($key)
            : $name;
    }

    protected function baseName(AttributeInterface $component): ?string
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
     * The qualifiers in the key's order, each with its name. The key sorts names and values as
     * given, so the order does not change with how they were added, and a value named here can
     * sort differently from the value itself.
     */
    protected function qualifiers(ComponentKey $key): string
    {
        $suffix = '';
        foreach ($key->qualifiers as $name => $values) {
            $values = array_map($this->qualifierValue(...), (array) $values);
            $suffix .= '-' . $this->clean($name) . ($values === [] ? '' : '.' . implode('_', $values));
        }

        return $suffix;
    }

    /**
     * A class is named like any other, so it reads the same as its own component.
     */
    protected function qualifierValue(bool|float|int|string $value): string
    {
        if (is_string($value) && (class_exists($value) || interface_exists($value))) {
            return $this->className(new \ReflectionClass($value));
        }

        return $this->clean(is_bool($value) ? var_export($value, true) : (string) $value);
    }

    /**
     * Anything a component name does not allow becomes `_`.
     */
    protected function clean(string $value): string
    {
        return (string) preg_replace('/[^a-zA-Z0-9._-]/', '_', $value);
    }

    /**
     * @param \ReflectionClass<object> $class
     */
    protected function className(\ReflectionClass $class): string
    {
        return $class->getShortName();
    }
}
