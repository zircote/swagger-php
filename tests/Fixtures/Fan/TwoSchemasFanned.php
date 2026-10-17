<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Fan;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'First')]
#[OA\Schema(component: 'Second')]
class TwoSchemasFanned
{
    #[OA\Property(fan: OA\Fan::All)]
    public string $name;
}
