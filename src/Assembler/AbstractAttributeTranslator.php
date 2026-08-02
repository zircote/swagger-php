<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Assembler;

use OpenApi\AttributeTranslatorInterface;

/**
 * Convenience empty/noop base imlementation of the `AttributeTranslatorInterface`.
 */
class AbstractAttributeTranslator implements AttributeTranslatorInterface
{
    public function reset(): void
    {
    }

    public function getAttributes(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        return [];
    }

    public function translate(array $attributes, array $created, \ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        return [...$attributes, ...$created];
    }
}
