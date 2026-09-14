<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

enum EnumClassStringAssetTypeSpec: string
{
    case Album = 'album';
    case Background = 'background';
}

#[OA\Info(title: 'Enum Class String Scratch', version: '1.0')]
#[OA\Operation\Get(
    path: '/api/asset/{type}',
    description: 'An endpoint',
    operationId: 'enumClassString',
    parameters: [
        new OA\Parameter\Path(
            name: 'type',
            schema: new OA\Schema(type: 'string', enum: [EnumClassStringAssetTypeSpec::class])
        ),
    ],
)]
#[OA\Response(response: 200, description: 'OK')]
class EnumClassStringEndpointSpec
{
}
