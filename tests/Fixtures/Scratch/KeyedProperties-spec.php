<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

/*
 * `properties` keyed by property name, with a `Schema` or a `Type` as the value, written
 * out as a list of `Property` in KeyedProperties.php. Both produce the same document.
 */

#[OA\Schema(
    component: 'KeyedPropertiesTerse',
    type: OA\Type::Object,
    properties: [
        'id' => OA\Type::Integer,
        'name' => OA\Type::String,
        'createdAt' => new OA\Schema(type: OA\Type::String, format: 'date-time'),
    ],
)]
class KeyedPropertiesTerseSpec
{
}

#[OA\Schema(
    component: 'KeyedPropertiesMixed',
    description: 'A keyed entry and a Property in one array keep the order they are written in',
    properties: [
        new OA\Property(property: 'first', schema: new OA\Schema(type: 'string')),
        'second' => OA\Type::Boolean,
    ],
)]
class KeyedPropertiesMixedSpec
{
}

#[OA\Schema(
    component: 'KeyedPropertiesTypeList',
    properties: [
        'count' => new OA\Schema(type: [OA\Type::Integer, OA\Type::Null]),
    ],
)]
class KeyedPropertiesTypeListSpec
{
}

#[OA\Info(title: 'Keyed Properties Scratch', version: '1.0')]
class KeyedPropertiesEndpointSpec
{
    #[OA\Operation\Post(path: '/things', operationId: 'createThing')]
    #[OA\RequestBody(required: true, content: new OA\MediaType\Json(
        type: OA\Type::Object,
        properties: [
            'name' => OA\Type::String,
            'tags' => new OA\Schema(type: OA\Type::Array, items: new OA\Schema(type: OA\Type::String)),
        ],
    ))]
    #[OA\Response(response: 200, description: 'OK')]
    public function create(): void
    {
    }
}
