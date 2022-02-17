<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Petstore30\Models;

/**
 * Pets Category.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @OA\Schema(
 *     title="Pets Category.",
 *     @OA\Xml(
 *         name="Category"
 *     )
 * )
 */
class Category
{
    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID",
     *     format="int64",
     * )
     *
     * @var int
     */
    private $id;

    /**
     * @OA\Property(
     *     title="Category name",
     *     description="Category name"
     * )
     *
     * @var string
     */
    private $name;
}
