<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Annotations as OA;

trait HasId
{
    /**
     * @OA\Property(
     *     format="int64",
     *     readOnly=true,
     * )
     */
    public int $id;
}

trait HasTimestamps
{
    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     readOnly=true,
     * )
     */
    public \DateTime $created_at;

    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     readOnly=true,
     * )
     */
    public \DateTime $updated_at;
}

abstract class Model
{
    use HasId;
}

/**
 * @OA\Schema(
 *     required={"street"},
 *     @OA\Xml(
 *         name="Address"
 *     )
 * )
 */
class Address extends Model
{
    use HasTimestamps;

    /** @OA\Property */
    public string $street;
}

/**
 * @OA\Info(title="API", version="1.0")
 * @OA\Get(
 *     path="/api/endpoint",
 *     @OA\Response(
 *         response=200,
 *         description="successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/Address")
 *     )
 * )
 */
class MergeTraitsEndpoint
{
}
