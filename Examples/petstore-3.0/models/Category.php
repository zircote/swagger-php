<?php

/**
 * @license Apache 2.0
 */

namespace Petstore30;

/**
 * Class Category
 *
 * @package Petstore30
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @OAS\Schema(
 *     type="object",
 *     description="Pets Category",
 *     title="Pets Category",
 *     @OAS\Xml(
 *         name="Category"
 *     )
 * )
 */
class Category
{
    /**
     * @OAS\Property(
     *     title="ID",
     *     description="ID",
     *     format="int64",
     * )
     *
     * @var integer
     */
    private $id;

    /**
     * @OAS\Property(
     *     title="Category name",
     *     description="Category name"
     * )
     *
     * @var string
     */
    private $name;
}
