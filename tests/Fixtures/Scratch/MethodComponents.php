<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Method components', version: '1.0')]
#[OAT\Get(
    path: '/things',
    operationId: 'getThings',
    parameters: [new OAT\Parameter(ref: '#/components/parameters/page')],
    responses: [
        new OAT\Response(response: 200, description: 'OK', content: new OAT\JsonContent(ref: '#/components/schemas/Thing')),
        new OAT\Response(response: 404, ref: '#/components/responses/notFound'),
    ],
)]
#[OAT\Post(
    path: '/things',
    operationId: 'postThing',
    requestBody: new OAT\RequestBody(ref: '#/components/requestBodies/newThing'),
    responses: [
        new OAT\Response(response: 201, description: 'Created', content: new OAT\JsonContent(ref: '#/components/schemas/Other')),
    ],
)]
class MethodComponentsEndpoint
{
}

// components declared on methods that exist only to carry them, rather than on the class
class MethodComponentsHolder
{
    #[OAT\Schema(schema: 'Thing', type: 'object', properties: [new OAT\Property(property: 'id', type: 'integer')])]
    public function thing(): void
    {
    }

    #[OAT\Schema(schema: 'Other', type: 'object', properties: [new OAT\Property(property: 'name', type: 'string')])]
    public function model(): void
    {
    }

    #[OAT\Parameter(parameter: 'page', name: 'page', in: 'query', schema: new OAT\Schema(type: 'integer'))]
    #[OAT\Response(response: 'notFound', description: 'Not found')]
    #[OAT\RequestBody(request: 'newThing', content: new OAT\JsonContent(ref: '#/components/schemas/Other'))]
    public function reusable(): void
    {
    }
}
