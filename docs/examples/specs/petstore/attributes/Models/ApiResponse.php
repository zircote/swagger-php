<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes\Models;

use OpenApi\Attributes as OAT;

/**
 * Class ApiResponse.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
#[OAT\Schema(title: 'Api response', description: 'Api response')]
class ApiResponse
{
    #[OAT\Property(title: 'Code', description: 'Code', format: 'int32')]
    private int $code;

    #[OAT\Property(title: 'Type', description: 'Type')]
    private string $type;

    #[OAT\Property(title: 'Message', description: 'Message')]
    private string $message;
}
