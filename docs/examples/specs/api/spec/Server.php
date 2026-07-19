<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Spec;

use OpenApi\Spec as OA;

/**
 * A Server.
 */
#[OA\Server(url: 'https://example.localhost', description: 'The local environment.')]
#[OA\Server(url: 'https://example.com', description: 'The production server.')]
class Server
{
}
