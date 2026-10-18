<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Fan;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'FannedSchema')]
class FannedSchema
{
    #[OA\Schema(description: 'shared', fan: OA\Fan::All)]
    #[OA\Property(property: 'first')]
    #[OA\Property(property: 'second')]
    public string $value;
}
