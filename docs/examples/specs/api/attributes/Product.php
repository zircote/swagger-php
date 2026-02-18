<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Attributes;

use OpenApi\Attributes as OAT;
use OpenApi\Examples\Specs\Api\ProductInterface;

/**
 * A Product description ignored.
 */
#[OAT\Schema(title: 'Product', description: 'A Product.')]
class Product implements ProductInterface
{
    use NameTrait;

    /**
     * The kind.
     */
    #[OAT\Property(property: 'kind')]
    public const KIND = 'Virtual';

    #[OAT\Property(description: 'The id.', format: 'int64', example: 1)]
    /**
     * The id.
     */
    public $id;

    public function __construct(
        #[OAT\Property()]
        public int $quantity,
        #[OAT\Property(example: null, default: null)]
        public ?string $brand,
        #[OAT\Property(description: 'The colour')]
        public Colour $colour,
        #[OAT\Property(type: 'string')]
        public \DateTimeInterface $releasedAt,
    ) {
    }
}
