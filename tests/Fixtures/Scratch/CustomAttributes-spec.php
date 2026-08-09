<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

// ======== custom attributes =======================

#[\Attribute(\Attribute::TARGET_CLASS)]
class CustomInfoSpec extends OA\Info
{
}

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class CustomSchemaSpec extends OA\Schema
{
}

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER)]
class CustomPropertySpec extends OA\Property
{
}

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
class CustomItemSpec extends OA\Property
{
    /**
     * @param class-string $of
     */
    public function __construct(
        string $of,
        ?string $description = null
    ) {
        parent::__construct(
            schema: new OA\Schema(
                ref: $of,
                title: str_replace('Spec', '', (new \ReflectionClass($of))->getShortName()),
                description: $description,
            )
        );
    }
}

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
class CustomListSpec extends OA\Property
{
    /**
     * @param class-string $of
     */
    public function __construct(string $of, ?string $description = null)
    {
        parent::__construct(
            schema: new OA\Schema\Items(
                title: str_replace('Spec', '', (new \ReflectionClass($of))->getShortName()),
                description: $description,
                ref: $of,
            ),
        );
    }
}

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class CustomGetSpec extends OA\Operation\Get
{
}

// ======== application code =======================

#[CustomSchemaSpec(schema: 'CAItemModel')]
class CAItemModelSpec
{
}

#[CustomSchemaSpec(schema: 'CAModel')]
class CAModelSpec
{
    #[CustomPropertySpec]
    public ?string $name;

    #[CustomItemSpec(of: CAItemModelSpec::class)]
    public readonly CAItemModelSpec $item;

    #[CustomListSpec(of: CAItemModelSpec::class)]
    public readonly array $items;
}

#[CustomInfoSpec(
    title: 'Extended Attributes Scratch',
    version: '1.0'
)]
#[CustomGetSpec(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'CAEndpoint',
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
class CAEndpointSpec
{
}
