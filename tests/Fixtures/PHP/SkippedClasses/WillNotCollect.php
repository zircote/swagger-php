<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP\SkippedClasses;

use OpenApi\Spec as OA;

// skipped by assembly: the class loads, but spec's `enum` is a list, so a bare string fails
// when the attribute is instantiated
#[OA\Operation\Get(path: '/will-not-collect', responses: [
    new OA\Response(response: 200, description: 'OK', content: new OA\MediaType\Json(schema: new OA\Schema(enum: 'one'))),
])]
class WillNotCollect
{
}
