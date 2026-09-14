<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

class NestedInlinePropertyPayload
{
}

#[OAT\Info(title: 'Nested Inline Property Scratch', version: '1.0')]
#[OAT\Schema(schema: 'NestedInlinePropertyEndpoint')]
class NestedInlinePropertyEndpoint
{
    #[OAT\Post(
        path: '/things',
        operationId: 'createThing',
        responses: [new OAT\Response(response: 200, description: 'OK')]
    )]
    #[OAT\RequestBody(required: true, content: new OAT\JsonContent(
        properties: [
            new OAT\Property(property: 'name', type: 'string'),
            new OAT\Property(property: 'count', type: 'integer'),
        ],
        type: 'object',
    ))]
    public function create(): NestedInlinePropertyPayload
    {
        return new NestedInlinePropertyPayload();
    }
}
