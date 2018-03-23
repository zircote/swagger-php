<?php

namespace Petstore;

/**
 * @OAS\Schema(schema="NewPet", type="object", required={"name"})
 */
class SimplePet
{

    public $id;

    /**
     * @OAS\Property()
     * @var string
     */
    public $name;

    /**
     * @var string
     * @OAS\Property()
     */
    public $tag;
}

/**
 *  @OAS\Schema(
 *   schema="Pet",
 *   type="object",
 *   allOf={
 *       @OAS\Schema(ref="#/components/schemas/NewPet"),
 *       @OAS\Schema(
 *           required={"id"},
 *           @OAS\Property(property="id", format="int64", type="integer")
 *       )
 *   }
 * )
 */
