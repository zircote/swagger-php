<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'XmlContentEquiv')]
class XmlContentEquivSpec
{
    #[OA\Property]
    public string $name = '';
}

#[OA\Info(title: 'XmlContentEquiv', version: '1.0')]
#[OA\Operation\Get(
    path: '/endpoint/xml-content',
    operationId: 'xmlContentEquiv1',
)]
#[OA\Response(
    response: 200,
    description: 'All good',
    content: [
        new OA\MediaType\Xml(ref: XmlContentEquivSpec::class),
    ]
)]
class XmlContentEquivEndpoint1Spec
{
}

#[OA\Operation\Get(
    path: '/endpoint/media-type',
    operationId: 'xmlContentEquiv2',
)]
#[OA\Response(
    response: 200,
    description: 'All good',
    content: [
        new OA\MediaType(
            mediaType: 'application/xml',
            schema: new OA\Schema(ref: XmlContentEquivSpec::class)
        ),
    ]
)]
class XmlContentEquivEndpoint2Spec
{
}

#[OA\Operation\Get(
    path: '/endpoint/xml-array',
    operationId: 'xmlContentEquiv3',
)]
#[OA\Response(
    response: 200,
    description: 'All good',
    content: [
        new OA\MediaType\Xml(items: new OA\Schema\Items(ref: XmlContentEquivSpec::class)),
    ]
)]
class XmlContentEquivEndpoint3Spec
{
}
