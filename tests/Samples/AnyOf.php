<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Samples;

use OpenApi\Attributes as OA;

#[OA\Schema]
class AnyOf
{
    #[OA\Property(
        type: 'array',
        property: 'colors',
        description: '',
        items: new OA\Items(
            anyOf: [
                new OA\Property(
                    type: 'object',
                    properties: [
                        new OA\Property(type: 'integer', property: 'id', example: 0, description: ''),
                        new OA\Property(type: 'string', property: 'color', example: 'green', description: ''),
                    ],
                ),
                new OA\Property(
                    type: 'object',
                    properties: [
                        new OA\Property(type: 'integer', property: 'id', example: 1, description: ''),
                        new OA\Property(type: 'string', property: 'color', example: 'blue', description: ''),
                    ],
                ),
            ],
        ),
    )]
    protected $foo;
}
