<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Attribute items', version: '1.0')]
#[OAT\Get(path: '/api/endpoint', operationId: 'endpoint', responses: [new OAT\Response(response: 200, description: 'OK')])]
class AttributeItemsApi
{
}

// an `items` or `additionalProperties` given without a type takes the type the docblock names,
// and keeps whatever else it says; one with a type of its own stays as written
#[OAT\Schema]
class AttributeItems
{
    /** @var list<positive-int> */
    #[OAT\Property(items: new OAT\Items())]
    public array $empty;

    /** @var list<positive-int> */
    #[OAT\Property(items: new OAT\Items(description: 'An id'))]
    public array $described;

    /** @var list<int> */
    #[OAT\Property(items: new OAT\Items(type: 'string'))]
    public array $typed;

    /** @var list<list<int>> */
    #[OAT\Property(items: new OAT\Items(items: new OAT\Items()))]
    public array $nested;

    /** @var array<string, positive-int> */
    #[OAT\Property(additionalProperties: new OAT\AdditionalProperties())]
    public array $map;

    /** @var list<positive-int> */
    #[OAT\Property(description: 'A count', items: new OAT\Items())]
    public array $shortcut;
}
