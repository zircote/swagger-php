<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Polymorphism\Spec;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Polymorphism', description: 'Polymorphism example', version: '1', contact: new OA\Contact(name: 'Swagger API Team'))]
#[OA\Server(url: 'https://example.localhost', description: 'API server')]
#[OA\Tag(name: 'api', description: 'API operations')]
class Controller
{
    #[OA\Operation\Get(
        path: '/test',
        operationId: 'test',
        description: 'Get test',
        tags: ['api'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Polymorphism',
                content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: Request::class))],
            ),
        ],
    )]
    public function getProduct($id)
    {
    }
}
