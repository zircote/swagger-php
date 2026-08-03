<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Webhooks\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(required: ['id', 'name'])]
final class Pet
{
    #[OA\Property]
    #[OA\Schema(format: 'int64')]
    public int $id;

    #[OA\Property]
    public string $name;

    #[OA\Property]
    public string $tag;
}
