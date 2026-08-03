<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Spec;

use OpenApi\Spec as OA;

/**
 * A Product description ignored.
 */
#[OA\Schema(title: 'Product', description: 'A Product.')]
class Product
{
    use NameTrait;

    /**
     * The kind.
     */
    #[OA\Property(property: 'kind')]
    public const KIND = 'Virtual';

    #[OA\Property(schema: new OA\Schema(format: 'int64'))]
    /**
     * The id.
     *
     * @example 1
     */
    public $id;

    public function __construct(
        #[OA\Property]
        #[OA\Schema]
        public int $quantity,
        #[OA\Property]
        #[OA\Schema(example: null, default: null)]
        public ?string $brand,
        #[OA\Property]
        #[OA\Schema(description: 'The colour')]
        public Colour $colour,
        #[OA\Property]
        #[OA\Schema(type: 'string')]
        public \DateTimeInterface $releasedAt,
    ) {
    }
}
