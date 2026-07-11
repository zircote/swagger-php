<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingRefs\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(
    schema: 'product_status',
    type: 'string',
    description: 'The status of a product',
    default: 'available',
    enum: ['available', 'discontinued'],
)]
class ProductStatus
{
}
