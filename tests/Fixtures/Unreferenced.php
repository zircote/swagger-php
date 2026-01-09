<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

#[OAT\OpenApi(
    info: new OAT\Info(title: 'Unreferenced', version: '1.0'),
    security: [[ 'bearerAuth' ]],
)]
#[OAT\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    description: 'Basic Auth',
)]
#[OAT\SecurityScheme(
    securityScheme: 'api_key',
    type: 'apiKey',
    in: 'header',
)]
#[OAT\Schema(
    schema: 'ExampleSchema',
    type: 'object',
    properties: [
        new OAT\Property(
            property: 'layouts',
            items: new OAT\Items(
                type: 'object',
                properties: [
                    new OAT\Property(property: 'desktop', ref: '#/components/schemas/ExampleNestedSchema'),
                ]
            ),
        ),
    ]
)]
#[OAT\Schema(
    schema: 'ExampleNestedSchema',
    type: 'object',
    properties: [
        new OAT\Property(property: 'rows', items: new OAT\Items(type: 'object')),
    ]
)]
class Unreferenced
{
    #[OAT\Get(
        path: '/path',
        parameters: [
            new OAT\QueryParameter(
                name: 'example_param',
                style: 'spaceDelimited',
                explode: true,
                required: true,
            ),
        ],
        security: [[ 'bearerAuth' ]]
    )]
    public function someEndpoint()
    {
    }
}
