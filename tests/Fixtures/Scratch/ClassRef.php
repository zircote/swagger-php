<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'YoYo')]
class ClassRef
{
}

#[OAT\Info(title: 'ClassRef', version: '1.0')]
#[OAT\Get(
    path: '/endpoint',
    responses: [
        new OAT\Response(
            response: 200,
            description: 'All good',
            content: new OAT\JsonContent(ref: ClassRef::class)
        ),
    ]
)]
class ClassRefEndpoint
{
}
