<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

require_once __DIR__ . '/CollectionClasses.php';

class CollectionRequests
{
    /**
     * @param CollectionBag $request
     */
    #[OA\Operation(path: '/collections', method: 'post', responses: [new OA\Response(response: 200, description: 'Stored')])]
    public function store(#[OA\RequestBody] $request): void
    {
    }
}
