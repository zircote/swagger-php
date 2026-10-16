<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Components holder', version: '1.0')]
#[OAT\Post(
    path: '/things',
    operationId: 'postThing',
    parameters: [new OAT\Parameter(ref: '#/components/parameters/dryRun')],
    requestBody: new OAT\RequestBody(ref: '#/components/requestBodies/newThing'),
    responses: [
        new OAT\Response(response: 201, description: 'Created', content: new OAT\JsonContent(ref: '#/components/schemas/thing')),
        new OAT\Response(response: 404, ref: '#/components/responses/notFound'),
    ],
)]
class ComponentsHolderEndpoint
{
}

// classic names nothing after a method, so the keys the spec holder infers are spelled out
class ComponentsHolder
{
    #[OAT\Schema(schema: 'owner', type: 'string')]
    public $owner;

    #[OAT\Schema(schema: 'thing', type: 'object', properties: [new OAT\Property(property: 'id', type: 'integer'), new OAT\Property(property: 'owner', ref: '#/components/schemas/owner')])]
    public function thing(): void
    {
    }

    #[OAT\Response(response: 'notFound', description: 'Not found')]
    public function notFound(): void
    {
    }

    #[OAT\RequestBody(request: 'newThing', content: new OAT\JsonContent(ref: '#/components/schemas/thing'))]
    public function newThing(): void
    {
    }

    #[OAT\Parameter(parameter: 'dryRun', name: 'dryRun', in: 'query', schema: new OAT\Schema(type: 'boolean'))]
    public function dryRunParameter(): void
    {
    }
}
