<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingRefs\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(description: 'The status of a product', type: 'string', enum: ['available', 'discontinued'], default: 'available', component: 'product_status')]
class ProductStatus
{
}
