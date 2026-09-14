<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

enum ServerVariableEnumClassStringEnvType: string
{
    case Prod = 'prod';
    case Staging = 'staging';
}

#[OAT\Info(title: 'Server Variable Enum Class String Scratch', version: '1.0')]
#[OAT\Server(
    url: '{env}.example.com',
    variables: [
        new OAT\ServerVariable(
            serverVariable: 'env',
            default: 'prod',
            enum: ServerVariableEnumClassStringEnvType::class
        ),
    ]
)]
class ServerVariableEnumClassStringInfo
{
}

#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'serverVariableEnumClassString',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class ServerVariableEnumClassStringEndpoint
{
}
