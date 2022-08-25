<?php

namespace OpenApi\Examples\SwaggerSpec\Petstore;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(required={"id", "name"})
 */
class Pet
{
    /**
     * @OA\Property(type="integer", format="int64")
     */
    public $id;

    /**
     * @OA\Property
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property
     *
     * @var string
     */
    public $tag;
}
