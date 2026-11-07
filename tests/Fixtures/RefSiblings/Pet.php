<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\RefSiblings;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'Pet')]
class Pet
{
    #[OA\Property]
    public string $name;
}
