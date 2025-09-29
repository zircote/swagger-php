<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Functions', version: '1.0')]
class FunctionsSpec
{
}

#[OAT\Get(
    path: '/endpoint',
    responses: [
        new OAT\Response(
            response: 200,
            description: 'All good'
        ),
    ]
)]
function functions_function(
    #[OAT\QueryParameter(name: 'name')] string $name
) {
}
