<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Fan;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'OverlappingTargetsProperty')]
class OverlappingTargetsProperty
{
    #[OA\Property]
    #[OA\Schema\Items(type: 'string')]
    #[OverlappingTargets]
    public array $sameSlot;

    #[OA\Property]
    #[OA\Schema\Items(type: 'string')]
    #[OverlappingTargets(itemsSlot: 'allOf[]')]
    public array $differentSlots;
}
