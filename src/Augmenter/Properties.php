<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\HybridBridge;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Type\TypeMapper;
use OpenApi\Undefined;
use OpenApi\Utils\PipeInterface;

/**
 * Infers an object schema's `required` list and its properties' `default` from the PHP declarations.
 *
 * A property is required when its PHP type is not nullable and it has no default, the rule a
 * parameter follows, with the default counted too: a value exists when the field is left out. A
 * promoted constructor property counts by its parameter's default. A property with no PHP type
 * adds nothing to `required`.
 *
 * A default is taken where it is a value the document can hold: a scalar, an enum case or an array
 * of them. An enum case gives the value its schema lists: a backed case's value, a unit case's
 * name. `null` is not taken, since the nullable type already says it, and neither is a value the
 * schema's type does not admit.
 *
 * What the attribute sets wins: an explicit `required` list is kept as written, and so is an
 * explicit `default`, `null` included. Schemas converted from classic annotations are left alone.
 *
 * Runs after `Augmenter\Types`, which names each property and gives it the type its default is
 * checked against.
 *
 * @implements PipeInterface<Specification>
 */
class Properties implements PipeInterface
{
    public function __invoke(mixed $payload): mixed
    {
        $payload->getWalker()->eachSchema(function (OA\Schema $schema): void {
            if (!$schema->properties || $schema->getMeta(HybridBridge::class) === true) {
                return;
            }

            $required = [];
            foreach ($schema->properties as $property) {
                $reflector = $property->getReflector();
                if (!$property instanceof OA\Property
                    || $property->property === null
                    || (!$reflector instanceof \ReflectionProperty && !$reflector instanceof \ReflectionParameter)
                ) {
                    continue;
                }

                $default = $this->defaultOf($reflector);

                if ($default === Undefined::UNDEFINED && $reflector->getType() !== null && !$reflector->getType()->allowsNull()) {
                    $required[] = $property->property;
                }

                $this->applyDefault($property, $default);
            }

            if ($schema->required === null && $required !== []) {
                $schema->required = $required;
            }
        });

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    /**
     * The member's PHP default, or `Undefined::UNDEFINED` where it has none.
     */
    protected function defaultOf(\ReflectionProperty|\ReflectionParameter $reflector): mixed
    {
        if ($reflector instanceof \ReflectionProperty && $reflector->isPromoted()) {
            foreach ($reflector->getDeclaringClass()->getConstructor()?->getParameters() ?? [] as $parameter) {
                if ($parameter->getName() === $reflector->getName()) {
                    $reflector = $parameter;
                    break;
                }
            }
        }

        if ($reflector instanceof \ReflectionParameter) {
            return $reflector->isDefaultValueAvailable() ? $reflector->getDefaultValue() : Undefined::UNDEFINED;
        }

        // an untyped property with no default reports `null`, which is not taken anyway
        return $reflector->hasDefaultValue() ? $reflector->getDefaultValue() : Undefined::UNDEFINED;
    }

    protected function applyDefault(OA\Property $property, mixed $default): void
    {
        $value = $default === Undefined::UNDEFINED ? null : $this->documentValue($default);
        if ($value === null) {
            return;
        }

        $schema = $property->schema ?? new OA\Schema();
        if (!Undefined::isDefault($schema->default) || !$this->admits($schema, $value, $default instanceof \UnitEnum)) {
            return;
        }

        $schema->default = $value;
        $property->schema = $schema;
    }

    /**
     * The value as the document holds it, or `null` where it cannot hold it.
     */
    protected function documentValue(mixed $value): mixed
    {
        if ($value instanceof \UnitEnum) {
            return TypeMapper::caseValue($value);
        }

        if (is_scalar($value)) {
            return $value;
        }

        if (!is_array($value)) {
            return null;
        }

        $values = [];
        foreach ($value as $key => $item) {
            $values[$key] = $this->documentValue($item);
            if ($values[$key] === null) {
                return null;
            }
        }

        return $values;
    }

    protected function admits(OA\Schema $schema, mixed $value, bool $fromEnum): bool
    {
        // a reference can only be checked where the value came from the enum it points to
        if ($schema->ref !== null) {
            return $fromEnum;
        }

        $types = (array) $schema->type;
        if ($types === []) {
            return true;
        }

        $valueType = TypeMapper::valueType($value);
        foreach ($types as $type) {
            // an integer is a number too, and an empty array is an empty object as well as an empty list
            if ($type === $valueType || ($type === 'number' && $valueType === 'integer') || ($type === 'object' && $value === [])) {
                return true;
            }
        }

        return false;
    }
}
