<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Examples\Specs\Api\ProductInterface;

/**
 * A Product.
 *
 * @OA\Schema(
 *     title="Product"
 * )
 */
class Product implements ProductInterface
{
    use NameTrait;

    /** @OA\Property */
    public int $quantity;

    /** @OA\Property(nullable=true, default=null, example=null) */
    public string $brand;

    /** @OA\Property */
    public Colour $colour;

    /**
     * The id.
     *
     * @OA\Property(format="int64", example=1)
     */
    public $id;

    /**
     * The kind.
     *
     * @OA\Property(property="kind")
     */
    public const KIND = 'Virtual';

    public function __construct(
        /**
         * @OA\Property(type="string")
         */
        public \DateTimeInterface $releasedAt,
    ) {
    }
}
