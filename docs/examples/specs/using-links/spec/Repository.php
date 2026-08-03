<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingLinks\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'repository')]
class Repository
{
    #[OA\Property]
    #[OA\Schema(type: 'string')]
    public $slug;

    #[OA\Property]
    public User $owner;
}
