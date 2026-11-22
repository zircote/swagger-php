<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'AttributeItems')]
class AttributeItems
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

    /** @var list<int> */
    #[OA\Property(schema: new OA\Schema(items: new OA\Schema(ref: '#/components/schemas/Id')))]
    public array $referenced;

    /** @var list<list<int>> */
    #[OA\Property(schema: new OA\Schema(items: new OA\Schema(items: new OA\Schema())))]
    public array $nested;

    /** @var array<string, positive-int> */
    #[OA\Property(schema: new OA\Schema(additionalProperties: new OA\Schema()))]
    public array $map;

    /** @var array<string, int> */
    #[OA\Property(schema: new OA\Schema(additionalProperties: false))]
    public array $closed;

    /** @var list<positive-int> */
    #[OA\Schema\Items(description: 'A count')]
    public array $shortcut;

    /** @var list<?int> */
    #[OA\Property(schema: new OA\Schema(items: new OA\Schema(nullable: false)))]
    public array $notNull;
}

class AttributeItemsController
{
    /**
     * @param list<positive-int> $ids
     */
    #[OA\Operation\Get(path: '/items')]
    #[OA\Response(response: 200, description: 'OK')]
    public function list(
        #[OA\Parameter\Query(schema: new OA\Schema(items: new OA\Schema()))]
        array $ids,
    ) {
    }
}
