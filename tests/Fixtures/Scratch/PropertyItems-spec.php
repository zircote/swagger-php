<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(
    schema: 'ItemDto',
    title: 'Item Dto',
    required: [
        'name',
    ],
)]
class ItemDtoSpec
{
    #[OA\Property]
    #[OA\Schema(example: 'Car')]
    public string $name;
}

#[OA\Schema(
    schema: 'PropertyItems',
    title: 'Property Items',
    required: [
        'list1',
    ],
)]
class PropertyItemsSpec
{
    #[OA\Property]
    #[OA\Schema\Items(
        description: 'Missing docblock',
        ref: ItemDtoSpec::class,
        minItems: 2,
    )]
    public ?array $list1;

    /**
     * @var ItemDtoSpec[] $list2
     */
    #[OA\Property]
    #[OA\Schema\Items(
        description: 'With docblock',
        ref: ItemDtoSpec::class,
        minItems: 1,
    )]
    public array $list2;

    /**
     * @var string[] $list3
     */
    #[OA\Property]
    #[OA\Schema(
        description: 'Simple type',
        items: new OA\Schema\Items(
            type: 'string',
            minLength: 2,
        ),
        maxItems: 5,
        minItems: 0,
    )]
    public array $list3;
}

#[OA\Info(
    title: 'Property Items Scratch',
    version: '1.0'
)]
#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'getInheritedFilters',
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
class PropertyItemsEndpointSpec
{
}
