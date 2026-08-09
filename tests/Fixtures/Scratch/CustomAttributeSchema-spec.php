<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class CustomAttributeSchemaSpec extends OA\Schema
{
}

#[CustomAttributeSchemaSpec(schema: 'MyClass')]
class MyClassSpec
{
}

#[OA\Info(
    title: 'Custom Attribute Schema Scratch',
    version: '1.0'
)]
#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'customAttributeSchemaEndpoint',
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
class CustomAttributeSchemaEndpointSpec
{
}
