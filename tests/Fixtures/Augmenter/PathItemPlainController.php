<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\PathItem(
    summary: 'Product operations',
    description: 'CRUD for products',
)]
class PathItemPlainController
{
    #[OA\Operation\Get(path: '/products')]
    #[OA\Response(response: 200, description: 'Product list')]
    public function index()
    {
    }

    #[OA\Operation\Get(path: '/products/{id}')]
    #[OA\Response(response: 200, description: 'Single product')]
    public function show(int $id)
    {
    }
}
