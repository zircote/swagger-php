<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

// The JSON Schema keywords added in 3.1, one component schema per family.
// A 3.0 document drops them all: if/then/else, prefixItems and the unevaluated
// keywords with a warning, the rest silently.

#[OA\Schema(
    schema: 'conditional',
    type: 'object',
    properties: [
        new OA\Property(property: 'country', schema: new OA\Schema(type: 'string')),
        new OA\Property(property: 'postalCode', schema: new OA\Schema(type: 'string')),
        new OA\Property(property: 'region', schema: new OA\Schema(type: 'string')),
    ],
    if: new OA\Schema(required: ['country']),
    then: new OA\Schema(required: ['postalCode']),
    else: new OA\Schema(required: ['region']),
)]
class SchemaKeywordsConditionalSpec
{
}

#[OA\Schema(
    schema: 'tuple',
    type: 'array',
    prefixItems: [
        new OA\Schema(type: 'string'),
        new OA\Schema(type: 'integer'),
    ],
    contains: new OA\Schema(type: 'string'),
    minContains: 1,
    maxContains: 3,
    unevaluatedItems: false,
    unevaluatedProperties: new OA\Schema(type: 'string'),
)]
class SchemaKeywordsTupleSpec
{
}

#[OA\Schema(
    schema: 'dependent',
    type: 'object',
    dependentRequired: ['creditCard' => ['billingAddress']],
    dependentSchemas: ['creditCard' => new OA\Schema(required: ['billingAddress'])],
)]
class SchemaKeywordsDependentSpec
{
}

#[OA\Schema(
    schema: 'embedded',
    type: 'string',
    contentEncoding: 'base64',
    contentMediaType: 'application/json',
    contentSchema: new OA\Schema(type: 'object'),
)]
class SchemaKeywordsEmbeddedSpec
{
}

#[OA\Schema(schema: 'decoded', type: 'object')]
class SchemaKeywordsDecodedSpec
{
}

// a nested schema slot takes a ref like any other schema position
#[OA\Schema(
    schema: 'envelope',
    type: 'string',
    contentMediaType: 'application/json',
    contentSchema: new OA\Schema\Ref(SchemaKeywordsDecodedSpec::class),
)]
class SchemaKeywordsEnvelopeSpec
{
}

#[OA\Info(title: 'SchemaKeywords', version: '1.0')]
class SchemaKeywordsControllerSpec
{
    #[OA\Operation\Get(path: '/endpoint', operationId: 'endpoint')]
    #[OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\MediaType\Json(ref: '#/components/schemas/conditional'),
    )]
    public function endpoint(): void
    {
    }
}
