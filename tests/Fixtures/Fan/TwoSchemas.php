<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Fan;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'First')]
#[OA\Schema(component: 'Second')]
class TwoSchemas
{
    #[OA\Property]
    public string $name;
}
