<?php
/**
 * Created by IntelliJ IDEA.
 * User: sonrac
 * Date: 3/12/18
 * Time: 10:25 AM
 */

namespace SwaggerTests\Fixtures;

/**
 * @OAS\Schema(
 *   schema="UsingVar",
 *   type="object",
 *   required={"name"}
 * )
 */
class UsingVar
{
    /**
     * @var string
     * @OAS\Property
     */
    private $name;

    /**
     * @var \DateTimeInterface
     * @OAS\Property(ref="#/components/schemas/date")
     */
    private $createdAt;
}
