<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class Types31
{
    #[OAT\Property(type: ['string', 'integer'])]
    public string|int $stringInteger = '';

    #[OAT\Property(type: ['string', 'number', 'integer', 'boolean', 'array', 'object', 'null'])]
    public mixed $massiveTypes = '';
}

#[OAT\OpenApi(openapi: '3.1.0')]
#[OAT\Info(
    title: 'List of types',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class Types31Endpoint
{
}
