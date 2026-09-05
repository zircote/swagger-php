<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Nullable', version: '1.0')]
class ApiSpec
{
}

#[OA\Schema(
    schema: 'MyDateTime',
    type: 'string',
    format: 'rfc3339-timestamp',
    externalDocs: new OA\ExternalDocumentation(
        description: '**RFC3339** IETF',
        url: 'https://tools.ietf.org/html/rfc3339'
    ),
    example: '2023-08-02T07:06:46+03:30'
)]
class MyDateTimeSpec
{
}

#[OA\Schema(schema: 'Nullable')]
class NullableSpec
{
    #[OA\Property]
    public ?string $firstname;

    #[OA\Property]
    #[OA\Schema(nullable: false)]
    public ?string $middlename;

    #[OA\Property]
    #[OA\Schema(nullable: true)]
    public ?string $lastname;

    #[OA\Property]
    public ?MyDateTimeSpec $birthdate;

    #[OA\Property]
    #[OA\Schema(nullable: true)]
    public MyDateTimeSpec $otherdate;

    #[OA\Property]
    public MyDateTimeSpec|null $anotherdate;

    #[OA\Property]
    #[OA\Schema(type: ['string', 'null'])]
    public ?string $description;

    #[OA\Property]
    #[OA\Schema(enum: ['Choice1', 'Choice2', null], example: 'Choice1', nullable: true)]
    public ?string $choice = null;

    public function __construct(
        #[OA\Property]
        #[OA\Schema(nullable: false)]
        public ?string $title,
    ) {
    }
}

#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'nullableEndpoint',
)]
#[OA\Response(response: 200, description: 'OK')]
class NullableEndpointSpec
{
}
