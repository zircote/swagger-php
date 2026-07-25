<?php

namespace Openapi\Snippets\Cookbook\XmlRootName;

use OpenApi\Spec as OA;

#[OA\Schema(
    schema: 'Error',
    properties: [new OA\Property(property: 'message')],
    xml: new OA\Xml(name: 'details'),
)]
#[OA\Operation\Post(
    path: '/foobar',
    responses: [
        new OA\Response(
            response: 400,
            description: 'Request error',
            content: [new OA\MediaType(
                mediaType: 'application/xml',
                schema: new OA\Schema(
                    ref: '#/components/schemas/Error',
                    xml: new OA\Xml(name: 'error'),
                ),
            )],
        ),
    ],
)]
class OpenApiSpec
{
}
