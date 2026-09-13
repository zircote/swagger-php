<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

// The JSON Schema keywords added in 3.1, one component schema per family.
// A 3.0 document drops them all: if/then/else, prefixItems and the unevaluated
// keywords with a warning, the rest silently — matching the spec compilers.

#[OAT\Schema(
    schema: 'conditional',
    type: 'object',
    properties: [
        new OAT\Property(property: 'country', type: 'string'),
        new OAT\Property(property: 'postalCode', type: 'string'),
        new OAT\Property(property: 'region', type: 'string'),
    ],
    if: new OAT\Schema(required: ['country']),
    then: new OAT\Schema(required: ['postalCode']),
    else: new OAT\Schema(required: ['region']),
)]
class SchemaKeywordsConditional
{
}

#[OAT\Schema(
    schema: 'tuple',
    type: 'array',
    prefixItems: [
        new OAT\Schema(type: 'string'),
        new OAT\Schema(type: 'integer'),
    ],
    contains: ['type' => 'string'],
    minContains: 1,
    maxContains: 3,
    unevaluatedItems: false,
    unevaluatedProperties: ['type' => 'string'],
)]
class SchemaKeywordsTuple
{
}

#[OAT\Schema(
    schema: 'dependent',
    type: 'object',
    dependentRequired: ['creditCard' => ['billingAddress']],
    dependentSchemas: ['creditCard' => new OAT\Schema(required: ['billingAddress'])],
)]
class SchemaKeywordsDependent
{
}

#[OAT\Schema(
    schema: 'embedded',
    type: 'string',
    contentEncoding: 'base64',
    contentMediaType: 'application/json',
    contentSchema: new OAT\Schema(type: 'object'),
)]
class SchemaKeywordsEmbedded
{
}

#[OAT\Schema(schema: 'decoded', type: 'object')]
class SchemaKeywordsDecoded
{
}

// a nested schema slot takes a ref like any other schema position
#[OAT\Schema(
    schema: 'envelope',
    type: 'string',
    contentMediaType: 'application/json',
    contentSchema: new OAT\Schema(ref: SchemaKeywordsDecoded::class),
)]
class SchemaKeywordsEnvelope
{
}

#[OAT\Info(title: 'SchemaKeywords', version: '1.0')]
class SchemaKeywordsController
{
    #[OAT\Get(
        path: '/endpoint',
        operationId: 'endpoint',
        responses: [
            new OAT\Response(
                response: 200,
                description: 'OK',
                content: new OAT\JsonContent(ref: '#/components/schemas/conditional'),
            ),
        ],
    )]
    public function endpoint(): void
    {
    }
}
