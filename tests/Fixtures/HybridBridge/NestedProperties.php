<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\HybridBridge;

use OpenApi\Attributes as OAT;

/**
 * Describes the schemas, not their properties.
 */
#[OAT\OpenApi(info: new OAT\Info(version: '1.0.0', title: 'Nested Properties'))]
#[OAT\Schema(schema: 'First', properties: [
    new OAT\Property(property: 'a', type: 'string'),
    new OAT\Property(property: 'b', type: 'string'),
])]
#[OAT\Schema(schema: 'Second', properties: [new OAT\Property(property: 'c', type: 'string')])]
class NestedProperties
{
}
