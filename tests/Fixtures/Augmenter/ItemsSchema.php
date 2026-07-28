<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Schema]
class ItemsSchema
{
    /** @var list<string> */
    #[OA\Property]
    #[OA\Schema\Items]
    public array $tags;
}
