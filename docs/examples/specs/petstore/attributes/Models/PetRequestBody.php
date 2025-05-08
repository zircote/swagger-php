<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes\Models;

use OpenApi\Attributes as OAT;

#[OAT\RequestBody(
    request: 'Pet',
    description: 'Pet object that needs to be added to the store',
    required: true,
    content: [
        new OAT\JsonContent(
            ref: Pet::class
        ),
        new OAT\MediaType(
            mediaType: 'application/xml',
            schema: new OAT\Schema(
                ref: Pet::class
            )
        ),
    ]
)]
class PetRequestBody
{
}
