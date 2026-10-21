<?php

namespace Openapi\Snippets\Cookbook\ListOfObjects;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="Pet")
 */
class Pet
{
    /**
     * @OA\Property(type="array", @OA\Items(ref="#/components/schemas/Tag"))
     */
    public array $tags;
}
