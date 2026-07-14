<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;
use OpenApi\Tests\Fixtures\Assembler\Attachable\InvalidMergePropertyAttachable;

#[OA\Schema(schema: 'WithInvalidAttachables')]
class WithInvalidAttachables
{
    #[OA\Property(attachables: [new OA\Attachable()])]
    #[InvalidMergePropertyAttachable]
    public string $name;
}
