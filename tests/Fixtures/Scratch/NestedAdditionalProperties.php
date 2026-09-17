<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(
    title: 'Nested Additional Properties',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'nestedAdditionalProperties',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
#[OAT\Schema(
    additionalProperties: new OAT\AdditionalProperties(
        additionalProperties: new OAT\AdditionalProperties(
            type: 'string',
            additionalProperties: false,
        )
    ),
    type: 'object'
)]
class NestedAdditionalProperties
{
}

/**
 * A map value type the resolver can read nested type info from. Deliberately carries no
 * annotations: the explicit `false` below suppresses the nested schema, so it is never
 * referenced — it exists to make `$schemaType->additionalProperties` a SchemaType.
 */
class NestedAdditionalPropertiesWidget
{
    public string $name = '';
}

// The inferred and the explicit meeting on one property: the PHP type says "map of Widget",
// the attribute says "no additional properties". The explicit false wins and the resolver
// must leave it alone rather than dereference it as a schema.
#[OAT\Schema(schema: 'ExplicitlyClosedMap', type: 'object')]
class NestedAdditionalPropertiesClosedMap
{
    /**
     * @var array<string, NestedAdditionalPropertiesWidget>
     */
    #[OAT\Property(property: 'items', additionalProperties: false)]
    public array $items = [];
}
