<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingRefs\Spec;

use OpenApi\Spec as OA;

class ProductParameter
{
    #[OA\Parameter(
        parameter: 'product_id_in_path_required',
        name: 'product_id',
        in: 'path',
        description: 'The ID of the product',
        required: true,
        schema: new OA\Schema(type: 'integer', format: 'int64'),
    )]
    public int $product_id;
}
