<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Nesting\Spec;

use OpenApi\Spec as OA;

/**
 * An entity controller class.
 */
#[OA\Info(title: 'Nested schemas', description: 'Example info', version: '1.0.0', contact: new OA\Contact(name: 'Swagger API Team'))]
#[OA\Server(url: 'https://example.localhost', description: 'API server')]
#[OA\Tag(name: 'api', description: 'All API endpoints')]
class ApiController
{
    #[OA\Operation\Get(
        path: '/entity/{id}',
        operationId: 'getEntity',
        description: 'Get the entity',
        tags: ['api'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'successful operation',
                content: [new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(ref: '#/components/schemas/ActualModel'),
                )],
            ),
        ],
    )]
    public function get($id)
    {
    }
}
