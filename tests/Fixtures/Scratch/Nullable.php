<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Nullable', version: '1.0')]
class Api
{
}

#[OAT\Schema(
    type: 'string',
    format: 'rfc3339-timestamp',
    externalDocs: new OAT\ExternalDocumentation(
        description: '**RFC3339** IETF',
        url: 'https://tools.ietf.org/html/rfc3339'
    ),
    example: '2023-08-02T07:06:46+03:30'
)]
class MyDateTime
{
}

#[OAT\Schema]
class Nullable
{
    #[OAT\Property]
    public ?string $firstname;

    #[OAT\Property(nullable: false)]
    public ?string $middlename;

    #[OAT\Property(nullable: true)]
    public ?string $lastname;

    #[OAT\Property]
    public ?MyDateTime $birthdate;

    #[OAT\Property(nullable: true)]
    public MyDateTime $otherdate;

    #[OAT\Property]
    public MyDateTime|null $anotherdate;

    #[OAT\Property(type: ['string', 'null'])]
    public ?string $description;

    #[OAT\Property(enum: ['Choice1', 'Choice2', null], example: 'Choice1', nullable: true)]
    public ?string $choice = null;

    public function __construct(
        #[OAT\Property(nullable: false)]
        public ?string $title,
    ) {
    }
}

#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class NullableEndpoint
{
}
