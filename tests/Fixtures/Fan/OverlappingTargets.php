<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Fan;

use OpenApi\Spec as OA;

/**
 * Names `Schema` and its subclass `Schema\Items` as merge targets, with the slot given per
 * construction, so one `Items` sibling matches it twice.
 */
#[\Attribute(\Attribute::TARGET_ALL | \Attribute::IS_REPEATABLE)]
class OverlappingTargets extends OA\Attachable
{
    public function __construct(public string $itemsSlot = 'attachables[]')
    {
        parent::__construct();
    }

    public function merge(): array
    {
        return [
            OA\Schema::class => 'attachables[]',
            OA\Schema\Items::class => $this->itemsSlot,
        ];
    }
}
