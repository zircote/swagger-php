<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Annotations\Models;

use OpenApi\Annotations as OA;

/**
 * Tag.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @OA\Schema(
 *     title="Tag",
 *     @OA\Xml(
 *         name="Tag"
 *     )
 * )
 */
class Tag
{
    /**
     * @OA\Property(
     *     format="int64",
     *     description="ID",
     *     title="ID"
     * )
     *
     * @var int
     */
    private $id;

    /**
     * @OA\Property(
     *     description="Name",
     *     title="Name"
     * )
     *
     * @var string
     */
    private $name;
}
