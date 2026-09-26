<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

/**
 * No PathItem of its own — governed by the ancestors'.
 */
class PathItemInheritedController extends PathItemUserController
{
    #[OA\Operation\Get(path: '/{id}/roles')]
    #[OA\Response(response: 200, description: 'User roles')]
    public function roles(int $id)
    {
    }
}
