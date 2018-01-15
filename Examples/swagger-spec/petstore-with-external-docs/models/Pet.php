<?php

/**
 * @OAS\Schema(
 *   definition="NewPet",
 *   type="object",
 *   required={"name"}
 * )
 */
class Pet
{

    public $id;
    /**
     * @OAS\Property(type="string")
     */
    public $name;

    /**
     * @OAS\Property(type="string")
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
