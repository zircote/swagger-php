<?php

namespace Openapi\Snippets\Cookbook\ListOfObjects;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'Pet')]
class Pet
{
    #[OA\Schema\Items(ref: '#/components/schemas/Tag')]
    public array $tags;
}
