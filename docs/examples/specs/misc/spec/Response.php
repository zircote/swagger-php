<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Misc\Spec;

use OpenApi\Spec as OA;

#[OA\Response(
    response: 200,
    description: 'Success',
    content: [
        new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(properties: [
                new OA\Property(property: 'name', schema: new OA\Schema(description: 'demo', type: 'integer')),
            ]),
            examples: [
                new OA\Example(example: '200', summary: '', value: ['name' => 1]),
                new OA\Example(example: '300', summary: '', value: ['name' => 1]),
                new OA\Example(example: '400', summary: '', value: ['name' => 1]),
            ],
        ),
    ],
)]
class Response
{
}
