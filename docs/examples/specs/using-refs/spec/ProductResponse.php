<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingRefs\Spec;

use OpenApi\Spec as OA;

#[OA\Response(description: 'All information about a product', content: [new OA\MediaType(
    mediaType: 'application/json',
    schema: new OA\Schema(ref: '#/components/schemas/Product'),
)], component: 'product')]
class ProductResponse
{
}
