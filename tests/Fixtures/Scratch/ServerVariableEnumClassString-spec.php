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
    url: '{env}.example.com:{port}',
    variables: [
        new OA\ServerVariable(
            serverVariable: 'env',
            default: 'prod',
            enum: [ServerVariableEnumClassStringEnvTypeSpec::class]
        ),
        new OA\ServerVariable(
            serverVariable: 'port',
            default: '443',
            enum: [8080, 443]
        ),
        new OA\ServerVariable(
            serverVariable: 'secure',
            default: 'yes',
            enum: [true, false]
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
