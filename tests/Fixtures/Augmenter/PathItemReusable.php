<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\PathItem(
    summary: 'Paginated collection',
    parameters: [
        new OA\Parameter\Query(name: 'page', schema: new OA\Schema(type: 'integer')),
        new OA\Parameter\Query(name: 'per_page', schema: new OA\Schema(type: 'integer')),
    ],
)]
class PathItemReusable
{
}
