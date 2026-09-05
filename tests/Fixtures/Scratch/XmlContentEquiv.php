<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class XmlContentEquiv
{
    #[OAT\Property]
    public string $name = '';
}

#[OAT\Info(title: 'XmlContentEquiv', version: '1.0')]
#[OAT\Get(
    path: '/endpoint/xml-content',
    operationId: 'xmlContentEquiv1',
    responses: [
        new OAT\Response(
            response: 200,
            description: 'All good',
            content: [
                new OAT\XmlContent(ref: XmlContentEquiv::class),
            ]
        ),
    ]
)]
class XmlContentEquivEndpoint1
{
}

#[OAT\Get(
    path: '/endpoint/media-type',
    operationId: 'xmlContentEquiv2',
    responses: [
        new OAT\Response(
            response: 200,
            description: 'All good',
            content: [
                new OAT\MediaType(
                    mediaType: 'application/xml',
                    schema: new OAT\Schema(ref: XmlContentEquiv::class)
                ),
            ]
        ),
    ]
)]
class XmlContentEquivEndpoint2
{
}

#[OAT\Get(
    path: '/endpoint/xml-array',
    operationId: 'xmlContentEquiv3',
    responses: [
        new OAT\Response(
            response: 200,
            description: 'All good',
            content: [
                new OAT\XmlContent(type: 'array', items: new OAT\Items(ref: XmlContentEquiv::class)),
            ]
        ),
    ]
)]
class XmlContentEquivEndpoint3
{
}
