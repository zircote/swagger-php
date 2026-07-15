<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;
use OpenApi\Tests\Fixtures\Assembler\Attachable\PropertyAttachable;

#[OA\Schema(schema: 'WithAttachables', attachables: [new OA\Attachable()])]
#[OA\Attachable]
#[OA\Attachable]
class WithAttachables
{
    #[OA\Property(attachables: [new OA\Attachable()])]
    #[PropertyAttachable]
    public string $name;
}
