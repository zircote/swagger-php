<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

/**
 * Fields added in OpenAPI 3.2, and the mixed-property sentinel that lets an explicit
 * `null` through.
 *
 * The 3.0 and 3.1 documents are the assertion that matters as much as the 3.2 one: every
 * 3.2-only field below has to be absent from them.
 */
#[OA\Security\Scheme\OAuth2(
    securityScheme: 'oauth2',
    description: 'Deprecated, and advertises its authorization server metadata.',
    deprecated: true,
    flows: [
        new OA\Flow\ClientCredentials(
            tokenUrl: 'https://auth.acme.dev/oauth/token',
            scopes: ['read:pets' => 'Read pets'],
        ),
    ],
    oauth2MetadataUrl: 'https://auth.acme.dev/.well-known/oauth-authorization-server',
)]
// the typed subtype has no oauth2MetadataUrl, so this one goes through the base class
#[OA\Security\Scheme(
    securityScheme: 'bearerAuth',
    type: OA\SchemeType::Http,
    description: 'oauth2MetadataUrl is set but applies to oauth2 only, so it is dropped.',
    deprecated: true,
    scheme: 'bearer',
    bearerFormat: 'JWT',
    oauth2MetadataUrl: 'https://auth.acme.dev/.well-known/oauth-authorization-server',
)]
class Spec32SecuritySchemesSpec
{
}

#[OA\Response(
    response: 'NotFound',
    summary: 'Nothing there',
    description: 'The pet does not exist',
)]
class Spec32NotFoundSpec
{
}

#[OA\OpenApi(self: 'https://spec.acme.dev/pets/openapi.yaml')]
#[OA\Info(title: 'Spec32', version: '1.0')]
#[OA\Server(
    url: 'https://api.acme.dev',
    description: 'Production',
    name: 'production',
)]
class Spec32DocumentSpec
{
}

#[OA\Operation\Get(
    path: '/pets',
    operationId: 'listPets',
    security: [
        new OA\Security\Requirement(scheme: 'bearerAuth'),
        new OA\Security\Requirement(scheme: 'oauth2', scopes: ['read:pets']),
    ],
    responses: [
        new OA\Response(
            response: 200,
            summary: 'All good',
            description: 'A list of pets',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(type: 'array', items: new OA\Schema(type: 'string'), nullable: true),
                examples: [
                    new OA\Example(
                        example: 'serialized',
                        summary: 'Both halves of the 3.2 example pair',
                        dataValue: ['Milo', 'Otis'],
                        serializedValue: '["Milo","Otis"]',
                    ),
                    new OA\Example(
                        example: 'nullData',
                        summary: 'An explicit null is a value, not an omission',
                        dataValue: null,
                    ),
                    new OA\Example(
                        example: 'nullValue',
                        summary: 'The same, for the pre-3.2 value field',
                        value: null,
                    ),
                    new OA\Example(
                        example: 'noValue',
                        summary: 'Nothing set, so nothing emitted',
                    ),
                ],
            ),
            links: [
                new OA\Link(
                    link: 'firstPet',
                    operationId: 'getPet',
                    description: 'An explicit null request body',
                    requestBody: null,
                ),
            ],
        ),
        new OA\Response(
            response: 404,
            summary: 'Dropped — a $ref response carries no siblings',
            ref: '#/components/responses/NotFound',
        ),
    ],
)]
#[OA\Operation\Get(
    path: '/pets/{id}',
    operationId: 'getPet',
    parameters: [
        new OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'string')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'One pet'),
    ],
)]
class Spec32EndpointSpec
{
}
