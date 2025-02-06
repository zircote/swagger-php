<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Mixed;

use OpenApi\Attributes as OAT;

#[OAT\Server(
    url: 'https://example.localhost',
    description: 'The local environment.'
)]
/**
 * A Server.
 */
#[OAT\Server(
    url: 'https://example.com',
    description: 'The production server.'
)]
class Server
{
}
