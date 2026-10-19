<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ContentSchema;

use OpenApi\Spec as OA;

#[OA\Schema]
class Pet
{
    #[OA\Property]
    public string $name;
}
