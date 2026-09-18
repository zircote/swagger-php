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
    url: '{env}.example.com:{port}',
    variables: [
        new OAT\ServerVariable(
            serverVariable: 'env',
            default: 'prod',
            enum: ServerVariableEnumClassStringEnvType::class
        ),
        // The spec says a server variable enum is [string], and a variable is substituted into
        // a URL, so a number is textual in the end and is coerced rather than refused.
        new OAT\ServerVariable(
            serverVariable: 'port',
            default: '443',
            enum: [8080, 443]
        ),
        // A bool has no textual form worth guessing at, so it is dropped with a warning —
        // leaving an enum that is empty, which is what the declaration actually said.
        new OAT\ServerVariable(
            serverVariable: 'secure',
            default: 'yes',
            enum: [true, false]
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
