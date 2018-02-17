<?php

/**
 * @license Apache 2.0
 */

namespace Petstore30;

/**
 * Class Pet
 *
 * @package Petstore30
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @OAS\Schema(
 *     description="Pet model",
 *     title="Pet model",
 *     type="object",
 *     required={"name", "photoUrls"},
 *     @OAS\Xml(
 *         name="Pet"
 *     )
 * )
 */
class Pet
{

    /**
     * @OAS\Property(
     *     format="int64",
     *     description="ID",
     *     title="ID",
     * )
     *
     * @var integer
     */
    private $id;

    /**
     * @OAS\Property(
     *     description="Category relation",
     *     title="Category",
     * )
     *
     * @var \Petstore30\Category
     */
    private $category;

    /**
     * @OAS\Property(
     *     format="int64",
     *     description="Pet name",
     *     title="Pet name",
     * )
     *
     * @var integer
     */
    private $name;

    /**
     * @OAS\Property(
     *     description="Photo urls",
     *     title="Photo urls",
     *     @OAS\Xml(
     *         name="photoUrl",
     *         wrapped=true
     *     ),
     *     @OAS\Items(
     *         type="string",
     *         default="images/image-1.png"
     *     )
     * )
     *
     * @var array
     */
    private $photoUrls;

    /**
     * @OAS\Property(
     *     description="Pet tags",
     *     title="Pet tags",
     *     @OAS\Xml(
     *         name="tag",
     *         wrapped=true
     *     ),
     * )
     *
     * @var \Petstore30\Tag[]
     */
    private $tags;
}
