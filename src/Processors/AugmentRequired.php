<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * Populate <code>Schema::$required</code> from its properties.
 *
 * A property with a boolean <code>required</code> value is always honoured; <code>true</code> adds it to the parent <code>Schema::$required</code> and <code>false</code> removes it.
 * The boolean is consumed so it never serialises on the property itself.
 * When the <code>augmentRequired.enabled</code> config flag is set, properties without an explicit boolean are inferred: a property backed by a PHP member (property, promoted parameter or method) becomes required when it has a known, non-nullable type.
 * Nullable properties, and properties whose type cannot be determined, are left optional.
 * Inference is skipped for a schema that already declares a <code>required</code> list, leaving that list as-is.
 *
 * Inference (the <code>augmentRequired.enabled</code> flag) is off by default; the boolean <code>required</code> handling always runs.
 */
class AugmentRequired
{
    protected bool $enabled;

    public function __construct(bool $enabled = false)
    {
        $this->enabled = $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Enables/disables the <code>AugmentRequired</code> processor.
     */
    public function setEnabled(bool $enabled): AugmentRequired
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function __invoke(Analysis $analysis): void
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class);

        foreach ($schemas as $schema) {
            if (!is_array($schema->properties) || $schema->properties === []) {
                continue;
            }

            $declared = !Generator::isDefault($schema->required);
            $required = $declared ? $schema->required : [];
            $changed = false;

            foreach ($schema->properties as $property) {
                // consume an explicit boolean regardless of the property name so it never serialises on the property
                $explicit = is_bool($property->required) ? $property->required : null;
                if (null !== $explicit) {
                    /* @phpstan-ignore assign.propertyType */
                    $property->required = Generator::UNDEFINED;
                }

                if (Generator::isDefault($property->property) || !is_string($property->property)) {
                    continue;
                }
                $name = $property->property;

                // an explicit boolean is always honoured: true adds the property, false removes it
                if (null !== $explicit) {
                    if (null !== $updated = $this->withRequired($required, $name, $explicit)) {
                        $required = $updated;
                        $changed = true;
                    }

                    continue;
                }

                // inference is opt-in and never touches an explicitly declared list
                if (!$this->enabled || $declared) {
                    continue;
                }

                if ($this->isInferredRequired($property) && null !== $updated = $this->withRequired($required, $name, true)) {
                    $required = $updated;
                    $changed = true;
                }
            }

            if ($changed) {
                $schema->required = $required === [] ? Generator::UNDEFINED : $required;
            }
        }
    }

    /**
     * Whether an unflagged property should be inferred as required.
     */
    protected function isInferredRequired(OA\Property $property): bool
    {
        // only infer from properties backed by a PHP member
        $reflector = $property->_context->reflector;
        if (!$reflector instanceof \ReflectionProperty
            && !$reflector instanceof \ReflectionParameter
            && !$reflector instanceof \ReflectionMethod) {
            return false;
        }

        if ($property->isNullable()) {
            return false;
        }

        // only mark required when a non-nullable type is actually known
        return !Generator::isDefault($property->type) || !Generator::isDefault($property->ref);
    }

    /**
     * Add or remove a property name in the required list.
     *
     * @param list<string> $required
     *
     * @return list<string>|null the updated list, or null when nothing changed
     */
    protected function withRequired(array $required, string $name, bool $include): ?array
    {
        $pos = array_search($name, $required, true);

        if ($include && false === $pos) {
            $required[] = $name;

            return $required;
        }

        if (!$include && false !== $pos) {
            unset($required[$pos]);

            return array_values($required);
        }

        return null;
    }
}
