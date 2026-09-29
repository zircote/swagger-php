<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'WithConstant')]
class WithConstant
{
    #[OA\Property(property: 'kind')]
    public const KIND = 'Virtual';
}
