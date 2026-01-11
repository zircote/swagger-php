<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class JsonContentEquiv
{
}

#[OAT\Info(title: 'JsonContentEquiv', version: '1.0')]
#[OAT\Get(
    path: '/endpoint/json-content',
    responses: [
        new OAT\Response(
            response: 200,
            description: 'All good',
            content: [
                new OAT\JsonContent(ref: JsonContentEquiv::class),
            ]
        ),
    ]
)]
class JsonContentEquivEndpoint1
{
}

#[OAT\Get(
    path: '/endpoint/media-type',
    responses: [
        new OAT\Response(
            response: 200,
            description: 'All good',
            content: [
                new OAT\MediaType(
                    mediaType: 'application/json',
                    schema: new OAT\Schema(ref: JsonContentEquiv::class)
                ),
            ]
        ),
    ]
)]
class JsonContentEquivEndpoint2
{
}
