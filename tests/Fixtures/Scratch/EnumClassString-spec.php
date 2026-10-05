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

// a property typed with the enum keeps its explicit enum; the enum is no component to reference
#[OA\Schema(component: 'EnumClassStringAsset')]
class EnumClassStringAssetSpec
{
    #[OA\Property(schema: new OA\Schema(enum: [EnumClassStringAssetTypeSpec::class]))]
    public EnumClassStringAssetTypeSpec $type;
}

#[OA\Operation\Get(
    path: '/api/asset',
    operationId: 'enumClassStringAsset',
)]
#[OA\Response(
    response: 200,
    description: 'OK',
    content: new OA\MediaType\Json(ref: EnumClassStringAssetSpec::class)
)]
class EnumClassStringAssetEndpointSpec
{
}
