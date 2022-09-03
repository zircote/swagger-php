<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Items;

#[Schema()]
class ReferencesEnum
{
    #[Property(title: 'statusEnum', description: 'Status enum', type: 'string', enum: StatusEnum::class, nullable: false)]
    public string $statusEnum;

    /**
     * @OA\Property(title="statusEnumBacked",
     *     description="Status enum backend",
     *     type="int",
     *     enum="\OpenApi\Tests\Fixtures\PHP\StatusEnumBacked",
     *     nullable="false"
     * )
     */
    public int $statusEnumBacked;

    #[Property(title: 'statusEnumIntegerBacked', description: 'Status enum integer backend', type: 'int', enum: StatusEnumIntegerBacked::class, nullable: true)]
    public ?int $statusEnumIntegerBacked;

    /**
     * @OA\Property(title="statusEnumStringBacked",
     *     description="Status enum string backend",
     *     type="string",
     *     enum="\OpenApi\Tests\Fixtures\PHP\StatusEnumStringBacked",
     *     nullable="true"
     * )
     */
    public ?string $statusEnumStringBacked;

    /** @var list<string> StatusEnumStringBacked array */
    #[Property(
        title: 'statusEnums',
        description: 'StatusEnumStringBacked array',
        type: 'array',
        items: new Items(title: 'itemsStatusEnumStringBacked', type: 'string', enum: StatusEnumStringBacked::class)
    )]
    public array $statusEnums;
}
