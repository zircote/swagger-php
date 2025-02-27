<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Tag(
    name: 'bool',
    x: ['custom-tag' => false],
)]
#[OAT\Tag(
    name: 'int',
    x: ['custom-tag' => 2],
)]
#[OAT\Tag(
    name: 'string',
    x: ['custom-tag' => 'foo'],
)]
class VendorExtensions
{
}

#[OAT\Info(
    title: 'Vendor Extensions Scratch',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class VendorExtensionsEndpoint
{
}
