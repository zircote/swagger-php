<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\Cookbook\Fragments;

use OpenApi\Spec as OA;

#[OA\Schema(description: 'A registered user')]
class User
{
    /**
     * @example 7
     */
    #[OA\Property(schema: new OA\Schema(readOnly: true))]
    public int $id;

    #[OA\Property(property: 'display_name')]
    public ?string $name = null;

    #[OA\Property]
    public Address $address;
}

#[OA\Schema]
class Address
{
    #[OA\Property]
    public string $street;
}
