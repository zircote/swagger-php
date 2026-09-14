<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

enum ServerVariableEnumClassStringEnvTypeSpec: string
{
    case Prod = 'prod';
    case Staging = 'staging';
}

#[OA\Info(title: 'Server Variable Enum Class String Scratch', version: '1.0')]
#[OA\Server(
    url: '{env}.example.com',
    variables: [
        new OA\ServerVariable(
            serverVariable: 'env',
            default: 'prod',
            enum: [ServerVariableEnumClassStringEnvTypeSpec::class]
        ),
    ]
)]
class ServerVariableEnumClassStringInfoSpec
{
}

#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'serverVariableEnumClassString',
)]
#[OA\Response(response: 200, description: 'OK')]
class ServerVariableEnumClassStringEndpointSpec
{
}
