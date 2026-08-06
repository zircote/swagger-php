<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'Types')]
class TypesSpec
{
    #[OA\Property]
    #[OA\Schema(type: ['string', 'integer'])]
    public string|int $stringInteger = '';

    #[OA\Property]
    #[OA\Schema(type: ['string', 'number', 'integer', 'boolean', 'object', 'null'])]
    public mixed $massiveTypes = '';
}

#[OA\OpenApi(version: '3.1.0')]
#[OA\Info(
    title: 'List of types',
    version: '1.0'
)]
#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'getTypes',
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
class TypesEndpointSpec
{
}
