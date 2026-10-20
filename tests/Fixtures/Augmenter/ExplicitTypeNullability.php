<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'ExplicitTypeNullability')]
class ExplicitTypeNullability
{
    #[OA\Property(schema: new OA\Schema(type: 'object'))]
    public ?array $explicit;

    #[OA\Property(schema: new OA\Schema(type: ['object', 'null']))]
    public ?array $explicitWithNull;

    #[OA\Property(schema: new OA\Schema(type: 'object', nullable: true))]
    public ?array $explicitNullable;

    #[OA\Property]
    public ?string $inferred;

    #[OA\Property(schema: new OA\Schema(items: new OA\Schema(type: 'string')))]
    public ?array $impliedByItems;

    #[OA\Property]
    #[OA\Schema\Items(type: 'string')]
    public ?array $itemsShortcut;
}
