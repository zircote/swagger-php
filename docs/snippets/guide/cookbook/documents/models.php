<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\Cookbook\Documents;

use OpenApi\Spec as OA;

#[OA\Schema(description: 'An order')]
class Order
{
    #[OA\Property]
    public int $id;

    #[OA\Property]
    public string $status;
}

class OrderController
{
    #[OA\Operation\Get(path: '/orders/latest', operationId: 'latestOrder', responses: [
        new OA\Response(response: 200, description: 'The latest order', content: [new OA\MediaType\Json(schema: new OA\Schema(ref: Order::class))]),
    ])]
    public function latest(): void
    {
    }
}
