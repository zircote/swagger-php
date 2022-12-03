<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Annotations as OA;

trait HasIdExtended
{
    /**
     * @OA\Property(
     *     format="int64",
     *     readOnly=true
     * )
     */
    public int $id;
}

trait HasTimestampsExtended
{
    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     readOnly=true
     * )
     */
    public \DateTime $created_at;

    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     readOnly=true
     * )
     */
    public \DateTime $updated_at;
}

trait HasSoftDeleteExtended
{
    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     readOnly=true
     * )
     */
    public ?\DateTime $deleted_at;
}

/**
 * @OA\Schema(
 *     description="This model can be ignored, it is just used for inheritance."
 * )
 *
 * @see BaseModel
 */
abstract class ModelExtended
{
    use HasIdExtended;
    use HasTimestampsExtended;
}

/**
 * @OA\Schema(
 *     description="Product",
 *     required={
 *         "number",
 *         "name"
 *     },
 *     @OA\Xml(
 *         name="Product"
 *     )
 * )
 *
 * @see ProductModel
 */
class Product extends ModelExtended
{
    use HasSoftDeleteExtended;

    /** @OA\Property */
    public string $number;

    /** @OA\Property */
    public string $name;
}

/**
 * @OA\Info(title="API", version="1.0")
 * @OA\Get(
 *     path="/api/endpoint",
 *     @OA\Response(
 *         response=200,
 *         description="successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     )
 * )
 */
class EndpointExtended
{
}
