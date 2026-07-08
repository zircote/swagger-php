<?php declare(strict_types=1);

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'WithConstant')]
class WithConstant
{
    #[OA\Property(property: 'kind')]
    public const KIND = 'Virtual';
}
