<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Tests\Fixtures\Assembler\Attachable\MutualFirstAttachable;
use OpenApi\Tests\Fixtures\Assembler\Attachable\MutualSecondAttachable;

class MutualMergeAttachables
{
    #[MutualFirstAttachable]
    #[MutualSecondAttachable]
    public function firstDeclaredFirst()
    {
    }

    #[MutualSecondAttachable]
    #[MutualFirstAttachable]
    public function secondDeclaredFirst()
    {
    }
}
