<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Assembler;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\AttributeTranslatorInterface;

/**
 * Convenience no-op base implementation of the `AttributeTranslatorInterface`.
 *
 * @phpstan-import-type AttributeReflector from AttributeTranslatorInterface
 */
class AbstractAttributeTranslator implements AttributeTranslatorInterface
{
    public function reset(): void
    {
    }

    /**
     * @param  AttributeReflector                  $reflector
     * @return array<\ReflectionAttribute<object>>
     */
    public function getAttributes(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        return [];
    }

    /**
     * @param  array<AttributeInterface> $attributes current attributes
     * @param  array<object>             $created    newly created attribute instances
     * @param  AttributeReflector        $reflector
     * @return array<AttributeInterface>
     */
    public function translate(array $attributes, array $created, \ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        return [...$attributes, ...$created];
    }
}
