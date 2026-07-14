<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\PathItem(prefix: '/orders')]
class PathItemLeafController extends PathItemMiddleController
{
    #[OA\Operation\Get(path: '')]
    #[OA\Response(response: 200, description: 'Order list')]
    public function index()
    {
    }

    #[OA\Operation\Get(path: '/{id}')]
    #[OA\Response(response: 200, description: 'Single order')]
    public function show(int $id)
    {
    }
}
