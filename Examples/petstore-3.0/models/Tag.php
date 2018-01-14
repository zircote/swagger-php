<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
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
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private $id;

    /**
     * @OAS\Property(
     *     description="Name",
     *     title="Name"
     * )
     *
     * @var string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private $name;
}