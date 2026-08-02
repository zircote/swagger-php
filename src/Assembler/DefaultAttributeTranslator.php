<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Assembler;

use OpenApi\AttributeInterface;

/**
 * Default implementation dealing with native attributes.
 */
class DefaultAttributeTranslator extends AbstractAttributeTranslator
{
    public function getAttributes(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        return $reflector->getAttributes(
            AttributeInterface::class,
            \ReflectionAttribute::IS_INSTANCEOF,
        );
    }
}
