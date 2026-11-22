<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Attribute items', version: '1.0')]
#[OA\Operation\Get(path: '/api/endpoint', operationId: 'endpoint')]
#[OA\Response(response: 200, description: 'OK')]
class AttributeItemsSpecApi
{
}

// an `items` or `additionalProperties` given without a type takes the type the docblock names,
// and keeps whatever else it says; one with a type of its own stays as written
#[OA\Schema(component: 'AttributeItems')]
class AttributeItemsSpec
{
    /** @var list<positive-int> */
    #[OA\Property(schema: new OA\Schema(items: new OA\Schema()))]
    public array $empty;

    /** @var list<positive-int> */
    #[OA\Property(schema: new OA\Schema(items: new OA\Schema(description: 'An id')))]
    public array $described;

    /** @var list<int> */
    #[OA\Property(schema: new OA\Schema(items: new OA\Schema(type: 'string')))]
    public array $typed;

    /** @var list<list<int>> */
    #[OA\Property(schema: new OA\Schema(items: new OA\Schema(items: new OA\Schema())))]
    public array $nested;

    /** @var array<string, positive-int> */
    #[OA\Property(schema: new OA\Schema(additionalProperties: new OA\Schema()))]
    public array $map;

    /** @var list<positive-int> */
    #[OA\Schema\Items(description: 'A count')]
    public array $shortcut;
}
