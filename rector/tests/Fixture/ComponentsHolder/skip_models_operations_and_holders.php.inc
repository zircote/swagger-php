<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolder;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'Pet')]
class Pet
{
    #[OA\Schema(schema: 'Name', type: 'string')]
    public string $name;
}

#[OA\Components]
class Holder
{
    #[OA\Schema(schema: 'Thing', type: 'object')]
    public function thing(): void
    {
    }
}

class Controller
{
    #[OA\Get(path: '/pets')]
    #[OA\Parameter(parameter: 'page', name: 'page', in: 'query')]
    public function list(): void
    {
    }

    #[OA\Get(path: '/pets/{id}')]
    #[OA\Response(response: 200, description: 'A pet')]
    #[OA\Header(header: 'X-Rate-Limit')]
    public function show(): void
    {
    }
}

class Unkeyed
{
    #[OA\Property(type: 'string')]
    public string $name;

    public function __construct(#[OA\Schema(schema: 'NotPromoted')] int $id)
    {
    }
}

?>
