<?php

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Error',
    properties: [new OA\Property(property: 'message')],
    xml: new OA\Xml(name: 'details'),
)]
#[OA\Post(
    path: '/foobar',
    responses: [
        new OA\Response(
            response: 400,
            description: 'Request error',
            content: new OA\XmlContent(
                ref: '#/components/schemas/Error',
                xml: new OA\Xml(name: 'error'),
            ),
        ),
    ],
)]
class OpenApiSpec
{
}
