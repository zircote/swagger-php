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
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Infers an object schema's `required` list and its properties' `default` from the PHP declarations.
 *
 * A property is required when its PHP type is not nullable and it has no default, the rule a
 * parameter follows, with the default counted too: a value exists when the field is left out. A
 * promoted constructor property counts by its parameter's default. A property with no PHP type
 * adds nothing to `required`. A property's own `required` replaces the rule for that property,
 * with or without a PHP member behind it. Where several properties share a name, a flag on any of
 * them replaces the rule, and flags that disagree leave the name out with a warning.
 *
 * A default is taken where it is a value the document can hold: a scalar, an enum case or an array
 * of them. An enum case gives the value its schema lists: a backed case's value, a unit case's
 * name. `null` is not taken, since the nullable type already says it, and neither is a value the
 * schema's type does not admit. An empty array becomes an empty object where the schema admits
 * objects but not arrays.
 *
 * What the attribute sets wins: an explicit `required` list is kept as written, and so is an
 * explicit `default`, `null` included. A property whose `required` contradicts an explicit list
 * gets a warning. Schemas converted from classic attributes and docblock annotations are left
 * alone.
 *
 * Runs after `Augmenter\Types`, which names each property and gives it the type its default is
 * checked against.
 *
 * @implements PipeInterface<Specification>
 */
class Properties implements PipeInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __invoke(mixed $payload): mixed
    {
        $payload->getWalker()->eachSchema(function (OA\Schema $schema): void {
            if (!$schema->properties || $schema->getMeta(HybridBridge::class) === true) {
                return;
            }

            $explicit = $schema->required;
            $inferred = [];
            $flagged = [];
            foreach ($schema->properties as $property) {
                if (!$property instanceof OA\Property || $property->property === null) {
                    continue;
                }

                $reflector = $property->getReflector();
                $hasMember = $reflector instanceof \ReflectionProperty || $reflector instanceof \ReflectionParameter;
                $default = $hasMember ? $this->defaultOf($reflector) : Undefined::UNDEFINED;

                if ($explicit !== null) {
                    $this->reportDisagreement($property, $explicit);
                } elseif ($property->required !== null) {
                    $inferred[$property->property] ??= false;
                    $flagged[$property->property][] = $property;
                } else {
                    $inferred[$property->property] = ($inferred[$property->property] ?? false)
                        || ($hasMember && $default === Undefined::UNDEFINED && $reflector->getType() instanceof \ReflectionType && !$reflector->getType()->allowsNull());
                }

                if ($hasMember) {
                    $this->applyDefault($property, $default);
                }
            }

            if ($explicit === null) {
                $required = $this->requiredNames($inferred, $flagged);
                if ($required !== []) {
                    $schema->required = $required;
                }
            }
        });

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    /**
     * The required names, in the order the properties first name them.
     *
     * A name's flags replace its inferred answer, and flags that disagree leave it out.
     *
     * @param  array<string, bool>              $inferred
     * @param  array<string, list<OA\Property>> $flagged
     * @return list<string>
     */
    protected function requiredNames(array $inferred, array $flagged): array
    {
        $required = [];
        foreach ($inferred as $name => $isInferred) {
            $flags = [];
            $last = null;
            foreach ($flagged[$name] ?? [] as $property) {
                $flags[(int) $property->required] = (bool) $property->required;
                $last = $property;
            }

            if ($last instanceof OA\Property && count($flags) > 1) {
                $this->logger?->warning(sprintf(
                    'Property "%s" has both required: true and required: false, left out of the required list in %s',
                    $name,
                    $last->getSourceLocation(),
                ));

                continue;
            }

            if ($flags !== [] ? in_array(true, $flags, true) : $isInferred) {
                $required[] = (string) $name;
            }
        }

        return $required;
    }

    /**
     * Warns where the property's own `required` contradicts the schema's explicit list.
     *
     * @param list<string> $list
     */
    protected function reportDisagreement(OA\Property $property, array $list): void
    {
        if ($property->required === null || in_array($property->property, $list, true) === $property->required) {
            return;
        }

        $this->logger?->warning(sprintf(
            'Property "%s" has required: %s, which the schema\'s required list contradicts; the list is kept in %s',
            $property->property,
            $property->required ? 'true' : 'false',
            $property->getSourceLocation(),
        ));
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

        $types = (array) $schema->type;
        if ($value === [] && in_array('object', $types, true) && !in_array('array', $types, true)) {
            $value = new \stdClass();
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
