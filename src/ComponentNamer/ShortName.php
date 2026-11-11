<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\ComponentNamer;

use OpenApi\Augmenter\Generics;
use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\ComponentNamerInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentKey;
use OpenApi\Type\TypeArguments;

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
 *
 * Type arguments, the `of` qualifier, are written by `TypeArgumentNames`, each argument named like
 * its own component: `Envelope<Customer>` is `Envelope-of.Customer`. A copy `Augmenter\Generics`
 * makes is named from the names it leaves on it: the copied schema's explicit name in place of the
 * class's, and an argument class's explicit name in place of its short name.
 */
class ShortName implements ComponentNamerInterface
{
    /** @var array<string, string> the explicit names of the classes in the type arguments of the component being named, by class */
    protected array $argumentNames = [];

    public function name(AttributeInterface $component): ?string
    {
        $name = $this->baseName($component);
        $key = $component->getComponentKey();

        if ($name === null || !$key instanceof ComponentKey || $key->isPlain()) {
            return $name;
        }

        $this->argumentNames = $this->copied($component)['arguments'] ?? [];
        try {
            return $name . $this->qualifiers($key);
        } finally {
            $this->argumentNames = [];
        }
    }

    protected function baseName(AttributeInterface $component): ?string
    {
        $base = $this->copied($component)['base'] ?? null;
        if ($base !== null) {
            return $base;
        }

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
            $arguments = $name === ComponentKey::TYPE_ARGUMENTS ? TypeArguments::of($key) : null;
            if ($arguments instanceof TypeArguments) {
                $suffix .= (new TypeArgumentNames($this->typeArgumentValue(...)))->write($arguments);

                continue;
            }

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
     * A value in type arguments: a class named as its own component would be, anything else as
     * given, since `TypeArgumentNames` escapes what a name does not allow rather than losing it.
     */
    protected function typeArgumentValue(string $value): string
    {
        return $this->argumentNames[$value] ?? (class_exists($value) || interface_exists($value) ? $this->className(new \ReflectionClass($value)) : $value);
    }

    /**
     * The names `Augmenter\Generics` left on a copy it made.
     *
     * @return array{base?: string, arguments?: array<string, string>}
     */
    protected function copied(AttributeInterface $component): array
    {
        $copied = $component->getMeta(Generics::class);

        return is_array($copied) ? $copied : [];
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
