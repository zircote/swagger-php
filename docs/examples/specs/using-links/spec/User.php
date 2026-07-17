<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingLinks\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'user')]
class User
{
    #[OA\Property(property: 'username')]
    #[OA\Schema(type: 'string')]
    public $username;

    #[OA\Property(property: 'uuid')]
    #[OA\Schema(type: 'string')]
    public $uuid;
}
