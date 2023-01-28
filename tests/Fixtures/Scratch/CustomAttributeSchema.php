<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class CustomAttributeSchema extends OAT\Schema
{
}

#[CustomAttributeSchema()]
class MyClass
{
}

#[OAT\Info(
    title: 'Custom Attribute Schema Scratch',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class CustomAttributeSchemaEndpoint
{
}
