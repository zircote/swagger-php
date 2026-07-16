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
     * @param  array<object>             $attributes
     * @return array<AttributeInterface>
     */
    public function translate(array $attributes): array;
}
