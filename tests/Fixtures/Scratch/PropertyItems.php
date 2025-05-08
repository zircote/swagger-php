<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    title: 'Item Dto',
    required: [
        'name',
    ],
)]
class ItemDto
{
    #[OAT\Property(example: 'Car')]
    public string $name;
}

#[OAT\Schema(
    title: 'Property Items',
    required: [
        'list1',
    ],
)]
class PropertyItems
{
    #[OAT\Property(
        description: 'Missing docblock',
        items: new OAT\Items(type: ItemDto::class),
        minItems: 2,
    )]
    public array $list1;

    /**
     * @var ItemDto[] $list2
     */
    #[OAT\Property(
        description: 'With docblock',
        items: new OAT\Items(type: ItemDto::class),
        minItems: 1,
    )]
    public array $list2;

    /**
     * @var string[] $list3
     */
    #[OAT\Property(
        description: 'Simple type',
        items: new OAT\Items(type: 'string', minLength: 2),
        maxItems: 5,
        minItems: 0,
    )]
    public array $list3;
}

#[OAT\Info(
    title: 'Property Items Scratch',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class PropertyItemsEndpoint
{
}
