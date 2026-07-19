<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingRefs\Spec;

use OpenApi\Spec as OA;

#[OA\RequestBody(
    request: 'product_in_body',
    description: 'product_request',
    required: true,
    content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(ref: '#/components/schemas/Product'),
    )],
)]
class ProductRequestBody
{
}
