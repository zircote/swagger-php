<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Assembler\Attachable;

use OpenApi\Spec as OA;

/**
 * Names {@see MutualFirstAttachable} as a merge target, which names this class back.
 */
#[\Attribute(\Attribute::TARGET_ALL | \Attribute::IS_REPEATABLE)]
class MutualSecondAttachable extends OA\Attachable
{
    public function merge(): array
    {
        return [
            MutualFirstAttachable::class => 'attachables[]',
        ];
    }
}
