<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Mixed;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;
use OpenApi\Examples\Specs\Api\ProductInterface;

#[OAT\Schema(title: 'Product', attachables: [new OAT\Attachable()])]
/**
 * A Product.
 */
class Product implements ProductInterface
{
    use NameTrait;

    /**
     * The kind.
     */
    #[OAT\Property(property: 'kind')]
    public const KIND = 'Virtual';

    /**
     * The id.
     *
     * @OA\Property(format="int64", example=1, @OA\Attachable)
     */
    public $id;

    #[OAT\Property(type: 'string')]
    public \DateTimeInterface $releasedAt;

    #[OAT\Property(property: 'quantity')]
    public function getQuantity(): int
    {
        return 1;
    }

<<<<<<< HEAD
    #[OAT\Property(nullable: true, default: null, example: null)]
=======
    #[OAT\Property(example: null, nullable: true, default: null)]
>>>>>>> ec21679 (Centralize all pure JSON Schema properties (#1960))
    public string $brand;

    /** @OA\Property(description="The colour") */
    public Colour $colour;
}
