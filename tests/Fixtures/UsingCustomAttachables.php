<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Tests\Fixtures\Attributes as OAF;
use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Custom annotation attributes', version: '1.0')]
#[OAT\Schema(
    schema: 'UsingCustomAttachables',
    required: ['name'],
    attachables: [
        new OAF\CustomAttachable(value: 1),
        new OAF\CustomAttachable(value: ['foo' => false, 'ping' => 'pong']),
    ]
)]
#[OAT\PathItem(path: '/')]
class UsingCustomAttachables
{
}
