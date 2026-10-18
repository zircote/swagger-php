<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\PathItem(parameters: [new OA\Parameter\Header(name: 'X-Tenant', schema: new OA\Schema(type: 'string'))])]
class PathItemSharedParameterController
{
    #[OA\Operation\Get(path: '/orders')]
    #[OA\Response(response: 200, description: 'Order list')]
    public function index()
    {
    }

    #[OA\Operation\Get(path: '/orders/{id}')]
    #[OA\Response(response: 200, description: 'Single order')]
    public function show(int $id)
    {
    }
}
