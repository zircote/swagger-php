<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\PathItem(
    parameters: [new OA\Parameter\Header(name: 'X-Request-Id', schema: new OA\Schema(type: 'string'))],
    security: [new OA\Security\Requirement(scheme: 'apiKey')],
    responses: [
        new OA\Response(response: 401, description: 'Unauthorized'),
        new OA\Response(response: 500, description: 'Server error'),
    ],
)]
class PathItemSharedResponseController
{
    #[OA\Operation\Get(path: '/items')]
    #[OA\Response(response: 200, description: 'Item list')]
    #[OA\Response(response: 401, description: 'Custom unauthorized')]
    public function index()
    {
    }

    #[OA\Operation\Post(path: '/items')]
    #[OA\Response(response: 201, description: 'Created')]
    public function create()
    {
    }
}
