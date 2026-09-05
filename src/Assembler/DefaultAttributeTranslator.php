<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Assembler;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\AttributeTranslatorInterface;

/**
 * Default implementation handling native (OpenApi) attributes.
 *
 * @phpstan-import-type AttributeReflector from AttributeTranslatorInterface
 */
class DefaultAttributeTranslator extends AbstractAttributeTranslator
{
    /**
     * @param  AttributeReflector                  $reflector
     * @return array<\ReflectionAttribute<object>>
     */
    public function getAttributes(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        return $reflector->getAttributes(
            AttributeInterface::class,
            \ReflectionAttribute::IS_INSTANCEOF,
        );
    }
}
