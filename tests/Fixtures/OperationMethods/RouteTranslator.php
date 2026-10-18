<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\OperationMethods;

use OpenApi\Assembler\AbstractAttributeTranslator;
use OpenApi\Spec as OA;

/**
 * Stands in for a framework route served for `PUT` and `PATCH`.
 */
class RouteTranslator extends AbstractAttributeTranslator
{
    public function translate(array $attributes, array $created, \ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        $translated = [...$attributes, ...$created];

        if ($reflector instanceof \ReflectionMethod && $reflector->getName() === 'update') {
            $translated[] = new OA\Operation(path: '/pets/{id}', methods: [OA\HttpMethod::Put, OA\HttpMethod::Patch]);
        }

        return $translated;
    }
}
