<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

/**
 * @OA\Schema(
 *   schema="UsingVar",
 *   required={"name"},
 *   @OA\Attachable(),
 *   @OA\Attachable()
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

/**
 * @OA\Schema(
 *   schema="date",
 *   type="datetime"
 * )
 */
class UsingVarSchema {}