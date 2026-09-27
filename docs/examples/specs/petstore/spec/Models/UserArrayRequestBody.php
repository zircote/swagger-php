<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Spec\Models;

use OpenApi\Spec as OA;

#[OA\RequestBody(description: 'List of user object', required: true, content: [
    new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: User::class)),
    ),
], component: 'UserArray')]
class UserArrayRequestBody
{
}
