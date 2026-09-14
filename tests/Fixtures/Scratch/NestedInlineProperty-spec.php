<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

class NestedInlinePropertyPayloadSpec
{
}

#[OA\Info(title: 'Nested Inline Property Scratch', version: '1.0')]
#[OA\Schema(schema: 'NestedInlinePropertyEndpoint')]
class NestedInlinePropertyEndpointSpec
{
    #[OA\Operation\Post(path: '/things', operationId: 'createThing')]
    #[OA\RequestBody(required: true, content: new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(
            type: 'object',
            properties: [
                new OA\Property(property: 'name', schema: new OA\Schema(type: 'string')),
                new OA\Property(property: 'count', schema: new OA\Schema(type: 'integer')),
            ],
        ),
    ))]
    #[OA\Response(response: 200, description: 'OK')]
    public function create(): NestedInlinePropertyPayloadSpec
    {
        return new NestedInlinePropertyPayloadSpec();
    }
}
