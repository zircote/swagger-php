<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

/*
 * The documents KeyedProperties-spec.php writes with keyed `properties`, spelled out as a list
 * of `Property`.
 */

#[OAT\Schema(
    schema: 'KeyedPropertiesTerse',
    properties: [
        new OAT\Property(property: 'id', type: 'integer'),
        new OAT\Property(property: 'name', type: 'string'),
        new OAT\Property(property: 'createdAt', type: 'string', format: 'date-time'),
    ],
    type: 'object',
)]
class KeyedPropertiesTerse
{
}

#[OAT\Schema(
    schema: 'KeyedPropertiesMixed',
    description: 'A keyed entry and a Property in one array keep the order they are written in',
    properties: [
        new OAT\Property(property: 'first', type: 'string'),
        new OAT\Property(property: 'second', type: 'boolean'),
    ],
)]
class KeyedPropertiesMixed
{
}

#[OAT\Schema(
    schema: 'KeyedPropertiesTypeList',
    properties: [
        new OAT\Property(property: 'count', type: ['integer', 'null']),
    ],
)]
class KeyedPropertiesTypeList
{
}

#[OAT\Schema(
    schema: 'KeyedPropertiesFormat',
    description: 'A Format brings the type it implies',
    properties: [
        new OAT\Property(property: 'id', type: 'string', format: 'uuid'),
        new OAT\Property(property: 'count', type: 'integer', format: 'int64'),
        new OAT\Property(property: 'ratio', type: 'number', format: 'double'),
        new OAT\Property(property: 'checkedAt', description: 'Format on a Schema', type: 'string', format: 'date-time'),
    ],
)]
class KeyedPropertiesFormat
{
}

#[OAT\Info(title: 'Keyed Properties Scratch', version: '1.0')]
class KeyedPropertiesEndpoint
{
    #[OAT\Post(
        path: '/things',
        operationId: 'createThing',
        responses: [new OAT\Response(response: 200, description: 'OK')]
    )]
    #[OAT\RequestBody(required: true, content: new OAT\JsonContent(
        properties: [
            new OAT\Property(property: 'name', type: 'string'),
            new OAT\Property(property: 'tags', type: 'array', items: new OAT\Items(type: 'string')),
        ],
        type: 'object',
    ))]
    public function create(): void
    {
    }
}
