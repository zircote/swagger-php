<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ComponentKey;

use OpenApi\Spec as OA;

// A keyed path item is a reusable component, not the path-bound metadata of this class: it
// governs no operation, takes no prefix, and lands under `components.pathItems`.
#[OA\PathItem(component: 'Paged', summary: 'A paged listing', parameters: [
    new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer')),
])]
class SharedPathItem
{
}

#[OA\Info(title: 'SharedPathItem', version: '1.0')]
#[OA\PathItem(prefix: '/api')]
class SharedPathItemController
{
    #[OA\Operation\Get(path: '/things', responses: [new OA\Response(response: 200, description: 'Things')])]
    public function things(): void
    {
    }
}
