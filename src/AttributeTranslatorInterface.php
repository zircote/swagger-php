<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

/**
 * Contract for creating raw attributes and translating them into `AttributeInterface` instances.
 *
 * Translator instances are long-lived (one per factory) and shared across all
 * `readAttributes()` calls within a collection pass. This means translators
 * can accumulate state across structural levels — e.g. tracking the current
 * `Operation` at method level and injecting into it at parameter level.
 *
 * Processing order within the Assembler is guaranteed structural:
 * class → method → parameters (outer before inner).
 */
interface AttributeTranslatorInterface
{
    /**
     * Reset any accumulated state between collection boundaries.
     *
     * Called by the assembler at the start of each top-level collection unit
     * (e.g. per class).
     */
    public function reset(): void;

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
     * @param  array<AttributeInterface> $attributes current attributes
     * @param  array<object>             $created    newly created attribute instances
     * @return array<AttributeInterface>
     */
    public function translate(array $attributes, array $created, \ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array;
}
