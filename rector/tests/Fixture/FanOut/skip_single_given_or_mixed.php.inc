<?php

namespace OpenApi\Rector\Tests\Fixture\FanOut;

use OpenApi\Spec as OAS;

class Orders
{
    #[OAS\Operation\Get(path: '/orders')]
    #[OAS\Response(response: 200, description: 'OK')]
    #[OAS\Header(header: 'X-Total', description: 'Total')]
    public function list(): void
    {
    }

    #[OAS\Operation\Get(path: '/orders/{id}')]
    #[OAS\Operation\Put(path: '/orders/{id}')]
    #[OAS\Response(response: 200, description: 'OK', fan: \OpenApi\Spec\Fan::Single)]
    public function show(): void
    {
    }

    #[OAS\Operation\Post(path: '/orders')]
    #[OAS\RequestBody(description: 'The order')]
    #[OAS\Response(response: 201, description: 'Created')]
    #[OAS\MediaType\Json]
    public function create(): void
    {
    }

    #[OAS\Operation\Delete(path: '/orders/{id}', responses: [new OAS\Response(response: 204, description: 'Gone')])]
    #[\Deprecated]
    public function delete(): void
    {
    }
}
