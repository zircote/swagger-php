<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingInterfaces\Spec;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Example of using interfaces in swagger-php', description: 'Using interfaces', version: '1.0.0', contact: new OA\Contact(name: 'Swagger API Team'))]
#[OA\Server(url: 'https://example.localhost', description: 'API server')]
#[OA\Tag(name: 'api', description: 'API operations')]
class OpenApiSpec
{
}
