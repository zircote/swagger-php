<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingRefs\Spec;

use OpenApi\Spec as OA;

class PropertyRefController
{
    #[OA\Operation\Get(
        path: '/products/{product_id}/do-other-stuff',
        operationId: 'doStuff',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(ref: '#/components/schemas/Product/properties/id'),
        ],
        responses: [
            new OA\Response(response: 'default', ref: '#/components/responses/todo'),
        ],
    )]
    public function doStuff($id)
    {
    }
}
