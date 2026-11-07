<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\RefSiblings;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'Owner')]
class Owner
{
    /**
     * The owner's pet.
     *
     * @deprecated
     */
    #[OA\Property]
    public Pet $pet;

    #[OA\Schema(description: 'Another pet')]
    public Pet $other;
}
