<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'DuplicateRef', version: '1.0')]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class DuplicateRefEndpoint
{
}

#[OAT\Schema(
    schema: 'abstract-user',
    properties: [
        new OAT\Property(property: 'name', type: 'string'),
        new OAT\Property(property: 'email', type: 'string'),
    ]
)]
class AbstractUser
{
}

#[OAT\Schema(
    schema: 'create-user',
    allOf: [
        new OAT\Schema(ref: '#/components/schemas/abstract-user'),
        new OAT\Schema(required: ['name', 'email']),
    ]
)]
class CreateUser extends AbstractUser
{
}
