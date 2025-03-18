<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class MultiTypeProperty
{
    #[OAT\Property(example: true)]
    public int|bool|null $value;
}

#[OAT\Info(
    title: 'Multi Typed Property Scratch',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class MultiTypePropertyEndpoint
{
}
