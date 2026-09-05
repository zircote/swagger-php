<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class AttributeInheritanceSchemaSpec extends OA\Schema
{
}

#[OA\Schema(schema: 'Base')]
class BaseSpec
{
    #[OA\Property]
    public int $id;
}

#[AttributeInheritanceSchemaSpec(schema: 'Child1')]
class Child1Spec extends BaseSpec
{
    #[OA\Property]
    public string $name;
}

#[OA\Schema(schema: 'Child2')]
class Child2Spec extends BaseSpec
{
    #[OA\Property]
    public string $title;
}

#[OA\Info(
    title: 'Attribute Inheritance Scratch',
    version: '1.0'
)]
#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'getEndpoint',
)]
#[OA\Response(response: 200, description: 'OK')]
class AttributeInheritanceEndpointSpec
{
}
