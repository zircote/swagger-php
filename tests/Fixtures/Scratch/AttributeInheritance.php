<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class AttributeInheritanceSchema extends OAT\Schema
{
}

#[OAT\Schema]
class Base
{
    #[OAT\Property]
    public int $id;
}

#[AttributeInheritanceSchema]
class Child1 extends Base
{
    #[OAT\Property]
    public string $name;
}

#[OAT\Schema]
class Child2 extends Base
{
    #[OAT\Property]
    public string $title;
}

#[OAT\Info(
    title: 'Attribute Inheritance Scratch',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class AttributeInheritanceEndpoint
{
}
