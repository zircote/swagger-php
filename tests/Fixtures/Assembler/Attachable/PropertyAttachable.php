<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Assembler\Attachable;

use OpenApi\Spec as OA;

#[\Attribute(\Attribute::TARGET_ALL | \Attribute::IS_REPEATABLE)]
class PropertyAttachable extends OA\Attachable
{
    public function merge(): array
    {
        return [
            OA\Property::class => 'attachables[]',
        ];
    }
}
