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

    #[OA\Property(property: 'id', schema: new OA\Schema(description: 'The id.', format: 'int64', example: 1))]
    /**
     * The id.
     */
    public $id;

    public function __construct(
        #[OA\Property(property: 'quantity')]
        public int $quantity,

        #[OA\Property(property: 'brand')]
        #[OA\Schema(example: null, default: null)]
        public ?string $brand,

        #[OA\Property(property: 'colour')]
        #[OA\Schema(description: 'The colour')]
        public Colour $colour,

        #[OA\Property(property: 'releasedAt')]
        #[OA\Schema(type: 'string')]
        public \DateTimeInterface $releasedAt,
    ) {
    }
}
