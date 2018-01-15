<?php

namespace Petstore;

/**
 * @OAS\Schema(definition="NewPet", type="object", required={"name"})
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
 *   definition="Pet",
 *   type="object",
 *   allOf={
 *       @OAS\Schema(ref="#/definitions/NewPet"),
 *       @OAS\Schema(
 *           required={"id"},
 *           @OAS\Property(property="id", format="int64", type="integer")
 *       )
 *   }
 * )
 */
