<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\PathItem(prefix: '/v2', tags: ['V2'])]
class PathItemMiddleController extends PathItemGrandparentController
{
}
