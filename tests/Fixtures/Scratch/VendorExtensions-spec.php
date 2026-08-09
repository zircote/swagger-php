<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Tag(
    name: 'bool',
    x: ['custom-tag' => false],
)]
#[OA\Tag(
    name: 'int',
    x: ['custom-tag' => 2],
)]
#[OA\Tag(
    name: 'string',
    x: ['custom-tag' => 'foo'],
)]
class VendorExtensionsSpec
{
}

#[OA\Info(
    title: 'Vendor Extensions Scratch',
    version: '1.0'
)]
#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'get',
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
class VendorExtensionsEndpointSpec
{
}
