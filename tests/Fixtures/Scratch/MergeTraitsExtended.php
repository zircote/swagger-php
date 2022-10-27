<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace App\Models;

use App\Models\Product as ProductModel;
use OpenApi\Annotations as OA;

trait HasId
{
    /**
     * @OA\Property(
     *     format="int64",
     *     readOnly=true
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

trait HasSoftDelete
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
abstract class Model
{
    use HasId;
    use HasTimestamps;
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
class Product extends Model
{
    use HasSoftDelete;

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
class Endpoint
{
}
