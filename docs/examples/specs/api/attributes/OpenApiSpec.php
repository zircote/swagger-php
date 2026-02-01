<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Attributes;

use OpenApi\Attributes as OAT;

/**
 * The Spec.
 */
#[OAT\OpenApi(openapi: OAT\OpenApi::VERSION_3_1_0, security: [['bearerAuth' => []]])]
#[OAT\Info(
    version: '1.0.0',
    title: 'Basic single file API',
    attachables: [new OAT\Attachable()]
)]
#[OAT\License(name: 'MIT', identifier: 'MIT')]
#[OAT\Server(url: 'https://localhost/api', description: 'API server')]
#[OAT\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', description: 'Basic Auth', scheme: 'bearer')]
#[OAT\Tag(name: 'products', description: 'All about products')]
#[OAT\Tag(name: 'catalog', description: 'Catalog API')]
class OpenApiSpec
{
}
