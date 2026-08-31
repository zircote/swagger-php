<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'UnnamedBooleanRequired',
    properties: [
        new OAT\Property(required: true),
    ]
)]
class UnnamedBooleanRequired
{
}
