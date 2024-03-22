<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class Types
{
    #[OAT\Property(type: ['string', 'integer'])]
    public string|int $stringInteger = '';

    #[OAT\Property(type: ['string', 'number', 'integer', 'boolean', 'array', 'object', 'null'])]
    public mixed $massiveTypes = '';
}

#[OAT\OpenApi(openapi: OAT\OpenApi::VERSION_3_1_0)]
#[OAT\Info(
    title: 'List of types',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class TypesEndpoint
{
}
