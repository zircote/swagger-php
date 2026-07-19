<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Spec\Models;

use OpenApi\Spec as OA;

#[OA\RequestBody(
    request: 'Pet',
    description: 'Pet object that needs to be added to the store',
    required: true,
    content: [
        new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(ref: Pet::class),
        ),
        new OA\MediaType(
            mediaType: 'application/xml',
            schema: new OA\Schema(ref: Pet::class),
        ),
    ],
)]
class PetRequestBody
{
}
