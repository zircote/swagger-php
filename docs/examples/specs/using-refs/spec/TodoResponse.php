<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingRefs\Spec;

use OpenApi\Spec as OA;

#[OA\Response(
    response: 'todo',
    description: 'This API call has no documentated response (yet)',
)]
class TodoResponse
{
}
