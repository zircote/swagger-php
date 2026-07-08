<?php declare(strict_types=1);

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;

class OrphanProperty
{
    #[OA\Property(property: 'orphan')]
    public string $orphan;
}
