<?php

namespace OpenApi\Examples\SwaggerSpec\PetstoreWithExternalDocs\Models;

/**
 * @OA\Schema(
 *     schema="Pet",
 *     type="object",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/NewPet"),
 *         @OA\Schema(
 *             required={"id"},
 *             @OA\Property(property="id", format="int64", type="integer")
 *         )
 *     }
 * )
 */
class Pet
{
}

/**
 * @OA\Schema(
 *     schema="NewPet",
 *     required={"name"}
 * )
 */
class NewPet
{
    public $id;
    /**
     * @OA\Property(type="string")
     */
    public $name;

    /**
     * @OA\Property(type="string")
     */
    public $tag;
}
