<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Spec\Models;

use OpenApi\Spec as OA;

/**
 * Class ApiResponse.
 */
#[OA\Schema(title: 'Api response', description: 'Api response')]
class ApiResponse
{
    #[OA\Property]
    #[OA\Schema(title: 'Code', description: 'Code', format: 'int32')]
    private int $code;

    #[OA\Property]
    #[OA\Schema(title: 'Type', description: 'Type')]
    private string $type;

    #[OA\Property]
    #[OA\Schema(title: 'Message', description: 'Message')]
    private string $message;
}
