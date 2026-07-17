<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingRefs\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'product_status', description: 'The status of a product', type: 'string', enum: ['available', 'discontinued'], default: 'available')]
class ProductStatus
{
}
