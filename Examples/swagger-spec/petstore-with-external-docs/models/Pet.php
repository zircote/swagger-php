<?php

/**
 * @SWG\Definition(
 *   definition="NewPet",
 *   type="object",
 *   required={"name"}
 * )
 */
class Pet
{

    public $id;
    /**
     * @SWG\Property(type="string")
     */
    public $name;

    /**
     * @SWG\Property(type="string")
     */
    public $tag;
}

/**
 *  @SWG\Definition(
 *   definition="Pet",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(ref="#/definitions/NewPet"),
 *       @SWG\Schema(
 *           required={"id"},
 *           @SWG\Property(property="id", format="int64", type="integer")
 *       )
 *   }
 * )
 */
