<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

enum EnumClassStringAssetType: string
{
    case Album = 'album';
    case Background = 'background';
}

#[OAT\Info(title: 'Enum Class String Scratch', version: '1.0')]
#[OAT\Get(
    path: '/api/asset/{type}',
    description: 'An endpoint',
    operationId: 'enumClassString',
    parameters: [
        new OAT\Parameter(
            name: 'type',
            in: 'path',
            required: true,
            schema: new OAT\Schema(type: 'string', enum: EnumClassStringAssetType::class)
        ),
    ],
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class EnumClassStringEndpoint
{
}
