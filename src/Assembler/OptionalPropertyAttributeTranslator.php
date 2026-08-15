<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Assembler;

use OpenApi\Spec as OA;

/**
 * Add the required `OA\Property` on schema properties that only have an `OA\Schema` attribute.
 */
class OptionalPropertyAttributeTranslator extends AbstractAttributeTranslator
{
    public function translate(array $attributes, array $created, \ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        $hasInstance = fn (array $list, string $class): bool => array_reduce(
            $list,
            static fn (bool $found, object $attribute): bool =>
                $found || $attribute instanceof $class,
            false
        );

        $translated = [...$attributes, ...$created];

        $hasSchema = $hasInstance($translated, OA\Schema::class);

        if ($reflector instanceof \ReflectionProperty
        || ($reflector instanceof \ReflectionParameter && $reflector->getDeclaringFunction()->getName() === '__construct')
        ) {
            $hasProperty = $hasInstance($translated, OA\Property::class);

            if ($hasSchema && !$hasProperty) {
                $translated = [new OA\Property(), ...$translated];
            }
        }

        return $translated;
    }
}
