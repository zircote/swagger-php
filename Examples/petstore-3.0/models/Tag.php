<?php

/**
 * @license Apache 2.0
 */

namespace Petstore30;

/**
 * Class Tag
 *
 * @package Petstore30
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @OAS\Schema(
 *     type="object",
 *     description="Tag",
 *     title="Tag",
 *     @OAS\Xml(
 *         name="Tag"
 *     )
 * )
 */
class Tag
{
    /**
     * @OAS\Property(
     *     format="int64",
     *     description="ID",
     *     title="ID"
     * )
     *
     * @var integer
     */
    private $id;

    /**
     * @OAS\Property(
     *     description="Name",
     *     title="Name"
     * )
     *
     * @var string
     */
    private $name;
}
