<?php
/**
 * Created by IntelliJ IDEA.
 * User: sonrac
 * Date: 3/12/18
 * Time: 10:25 AM
 */

namespace OpenApiTests\Fixtures;

/**
 * @OA\Schema(
 *   schema="UsingVar",
 *   type="object",
 *   required={"name"}
 * )
 */
class UsingVar
{
    /**
     * @var string
     * @OA\Property
     */
    private $name;

    /**
     * @var \DateTimeInterface
     * @OA\Property(ref="#/components/schemas/date")
     */
    private $createdAt;
}
