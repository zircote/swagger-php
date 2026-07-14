<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\PathItem(ref: PathItemReusable::class)]
class PathItemRefController
{
    #[OA\Operation\Get(path: '/articles')]
    #[OA\Response(response: 200, description: 'Article list')]
    public function list()
    {
    }
}
