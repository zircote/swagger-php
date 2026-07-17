<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Misc\Spec;

use OpenApi\Spec as OA;

#[OA\OpenApi(version: '3.1.0', security: [
    new OA\Security\Requirement(scheme: 'bearerAuth', scopes: []),
])]
#[OA\Info(
    title: 'Testing annotations from bugreports',
    description: "NOTE:\nThis sentence is on a new line",
    version: '1.0.0',
    contact: new OA\Contact(name: 'Swagger API Team'),
    license: new OA\License(name: 'apache', url: 'https://github.com/zircote/swagger-php/blob/master/LICENSE'),
)]
#[OA\Tag(name: 'endpoints')]
#[OA\Server(
    url: '{schema}://host.dev',
    description: 'OpenApi parameters',
    variables: [new OA\ServerVariable(serverVariable: 'schema', default: 'https', enum: ['https', 'http'])],
)]
#[OA\Security\Scheme\Http(securityScheme: 'bearerAuth', scheme: 'bearer')]
class OpenApiSpec
{
}
