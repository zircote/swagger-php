<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

// ======== custom attributes =======================

#[\Attribute(\Attribute::TARGET_CLASS)]
class CustomInfo extends OAT\Info
{
}

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class CustomSchema extends OAT\Schema
{
}

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER)]
class CustomProperty extends OAT\Property
{
}

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
class CustomItem extends OAT\Property
{
    /** @param class-string $of */
    public function __construct(
        string $of,
        ?string $description = null
    ) {
        parent::__construct(
            ref: $of,
            title: (new \ReflectionClass($of))->getShortName(),
            description: $description,
        );
    }
}

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
class CustomList extends OAT\Property
{
    /** @param class-string $of */
    public function __construct(string $of, ?string $description = null)
    {
        parent::__construct(
            title: (new \ReflectionClass($of))->getShortName(),
            description: $description,
            items: new OAT\Items(ref: $of)
        );
    }
}

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class CustomGet extends OAT\Get
{
}

// ======== application code =======================

#[CustomSchema]
class CAItemModel
{
}

#[CustomSchema]
class CAModel
{
    #[CustomProperty]
    public ?string $name;

    #[CustomItem(of: CAItemModel::class)]
    public readonly CAItemModel $item;

    #[CustomList(of: CAItemModel::class)]
    public readonly array $items;
}

#[CustomInfo(
    title: 'Extended Attributes Scratch',
    version: '1.0'
)]
#[CustomGet(
    path: '/api/endpoint',
    description: 'An endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class CAEndpoint
{
}
