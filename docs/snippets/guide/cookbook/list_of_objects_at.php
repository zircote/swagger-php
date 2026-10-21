<?php

namespace Openapi\Snippets\Cookbook\ListOfObjects;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'Pet')]
class Pet
{
    #[OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/Tag'))]
    public array $tags;
}
