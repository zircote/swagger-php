<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes\Models;

use OpenApi\Attributes as OAT;

#[OAT\RequestBody(
    request: 'UserArray',
    description: 'List of user object',
    required: true,
    content: new OAT\JsonContent(
        type: 'array',
        items: new OAT\Items(
            ref: User::class
        )
    )
)]
class UserArrayRequestBody
{
}
