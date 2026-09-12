<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'InlineProperties',
    properties: [
        new OAT\Property(property: 'inlineTyped', type: 'string'),
        new OAT\Property(property: 'inlineFlagged', type: 'string', required: true),
    ]
)]
class InlineProperties
{
}
