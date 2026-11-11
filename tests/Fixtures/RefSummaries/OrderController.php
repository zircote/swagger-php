<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\RefSummaries;

use OpenApi\Spec as OA;

#[OA\PathItem(prefix: '/orders', responses: [new OA\Response(response: 'default', description: 'Error', summary: 'Shared')])]
class OrderController
{
    #[OA\Operation\Get(path: '/list')]
    #[OA\Response(response: 200, description: 'Orders')]
    public function list(): void
    {
    }

    #[OA\Operation(path: '/{id}', methods: [OA\HttpMethod::Get, OA\HttpMethod::Head])]
    #[OA\Response(
        response: 200,
        description: 'The order',
        headers: [new OA\Header(header: 'X-Rate', description: 'Rate', summary: 'Inline header')],
    )]
    public function show(
        #[OA\Parameter\Path(name: 'id', summary: 'Inline parameter')]
        int $id,
    ): void {
    }
}
