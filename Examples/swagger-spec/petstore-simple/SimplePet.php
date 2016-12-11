<?php

namespace Petstore;

/**
 * @SWG\Definition(definition="NewPet", type="object", required={"name"})
 */
class SimplePet
{

    public $id;

    /**
     * @SWG\Property()
     * @var string
     */
    public $name;

    /**
     * @var string
     * @SWG\Property()
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
