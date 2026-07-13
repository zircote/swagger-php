<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\PathItem(
    prefix: '/users',
    tags: ['Users'],
)]
#[OA\Security\Requirement(scheme: 'bearerAuth')]
#[OA\Parameter\Path(name: 'tenant', schema: new OA\Schema(type: 'string'))]
class PathItemUserController extends PathItemBaseController
{
    #[OA\Operation\Get(path: '/list')]
    #[OA\Response(response: 200, description: 'User list')]
    public function list()
    {
    }

    #[OA\Operation\Get(path: '/{id}')]
    #[OA\Response(response: 200, description: 'Single user')]
    public function get(int $id)
    {
    }
}
