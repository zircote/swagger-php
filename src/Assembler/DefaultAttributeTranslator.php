<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Assembler;

use OpenApi\AttributeInterface;
use OpenApi\AttributeTranslatorInterface;

/**
 * Default implementation dealing with native attributes.
 */
class DefaultAttributeTranslator implements AttributeTranslatorInterface
{
    public function getAttributes(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        return $reflector->getAttributes(
            AttributeInterface::class,
            \ReflectionAttribute::IS_INSTANCEOF,
        );
    }

    public function translate(array $attributes): array
    {
        return $attributes;
    }
}
