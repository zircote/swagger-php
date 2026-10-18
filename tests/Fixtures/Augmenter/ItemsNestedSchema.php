<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Schema]
class ItemsNestedSchema
{
    #[OA\Schema(oneOf: [new OA\Schema\Items(type: 'string'), new OA\Schema(type: 'null')])]
    public ?array $oneOf;

    #[OA\Schema(allOf: [new OA\Schema\Items(type: 'string')])]
    public array $allOf;

    #[OA\Schema(type: 'array', items: new OA\Schema\Items(type: 'string'))]
    public array $items;

    #[OA\Schema(not: new OA\Schema\Items(type: 'string'))]
    public array $not;

    #[OA\Schema(type: 'object', additionalProperties: new OA\Schema\Items(type: 'string'))]
    public array $additionalProperties;
}
