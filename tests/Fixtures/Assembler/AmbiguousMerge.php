<?php declare(strict_types=1);

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'AmbiguousMerge')]
class AmbiguousMerge
{
    #[OA\Schema(description: 'ambiguous')]
    #[OA\Property(property: 'first')]
    #[OA\Property(property: 'second')]
    public string $value;
}
