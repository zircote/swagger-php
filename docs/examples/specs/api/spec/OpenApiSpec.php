<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec\Fixtures\Api;

use OpenApi\Spec as OA;

/**
 * The Spec.
 */
#[OA\OpenApi(version: '3.1.0', security: [['bearerAuth' => []]])]
#[OA\Info(
    title: 'Basic single file API',
    version: '1.0.0',
    license: new OA\License(name: 'MIT', identifier: 'MIT'),
)]
#[OA\Server(url: 'https://localhost/api', description: 'API server')]
#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', description: 'Basic Auth', scheme: 'bearer')]
#[OA\Tag(name: 'products', description: 'All about products')]
#[OA\Tag(name: 'catalog', description: 'Catalog API')]
class OpenApiSpec
{
}
