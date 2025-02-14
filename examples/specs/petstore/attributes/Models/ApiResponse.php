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
#[OAT\Schema(
    description: 'Api response',
    title: 'Api response'
)]
class ApiResponse
{
    #[OAT\Property(
        description: 'Code',
        title: 'Code',
        format: 'int32'
    )]
    private int $code;

    #[OAT\Property(
        description: 'Type',
        title: 'Type'
    )]
    private string $type;

    #[OAT\Property(
        description: 'Message',
        title: 'Message'
    )]
    private string $message;
}
