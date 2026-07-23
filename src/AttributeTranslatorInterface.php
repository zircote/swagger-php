<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

/**
 * Contract for creating raw attributes and translating them into `AttributeInterface` instances.
 */
interface AttributeTranslatorInterface
{
    /**
     * Get attributes to load from the given reflector.
     *
     * @return array<\ReflectionAttribute>
     */
    public function getAttributes(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array;

    /**
     * Translates the given list of attributes into `AttributeInterface` instances.
     *
     * When multiple translators are chained, each receives the cumulative result
     * from prior translators — a mix of already-resolved `AttributeInterface`
     * instances and newly instantiated objects from the current translator's
     * `getAttributes()` call.
     *
     * @param  array<object|AttributeInterface> $attributes
     * @return array<AttributeInterface>
     */
    public function translate(array $attributes): array;
}
