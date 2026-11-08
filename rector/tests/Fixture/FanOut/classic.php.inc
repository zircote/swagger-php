<?php

namespace OpenApi\Rector\Tests\Fixture\FanOut;

use OpenApi\Attributes as OA;

class Accounts
{
    #[OA\Get(path: '/accounts/{id}')]
    #[OA\Post(path: '/accounts/{id}')]
    #[OA\Response(response: 200, description: 'OK')]
    #[OA\Response(response: 404, description: 'Not found')]
    public function show(): void
    {
    }

    #[OA\Get(path: '/accounts', responses: [new OA\Response(response: 200, description: 'OK')])]
    #[OA\Delete(path: '/accounts', responses: [new OA\Response(response: 204, description: 'Gone')])]
    #[OA\QueryParameter(name: 'q')]
    public function search(#[OA\QueryParameter(name: 'page')] int $page): void
    {
    }

    #[OA\Get(path: '/limits')]
    #[OA\Response(response: 200, description: 'OK')]
    #[OA\Response(response: 429, description: 'Slow down')]
    #[OA\Header(header: 'X-Rate-Limit', description: 'Requests left')]
    #[OA\Link(link: 'self', operationId: 'limits')]
    public function limits(): void
    {
    }

    #[OA\Post(path: '/upload', responses: [new OA\Response(response: 200, description: 'OK')])]
    #[OA\Put(path: '/upload', responses: [new OA\Response(response: 200, description: 'OK')])]
    #[OA\RequestBody(description: 'The file')]
    public function upload(): void
    {
    }

    #[OA\Get(path: '/filter', responses: [new OA\Response(response: 200, description: 'OK')])]
    #[OA\QueryParameter(name: 'from')]
    #[OA\QueryParameter(name: 'to')]
    #[OA\Examples(example: 'today', summary: 'Today', value: 'today')]
    public function filter(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\FanOut;

use OpenApi\Attributes as OA;

class Accounts
{
    #[OA\Get(path: '/accounts/{id}')]
    #[OA\Post(path: '/accounts/{id}')]
    #[OA\Response(response: 200, description: 'OK', fan: \OpenApi\Spec\Fan::All)]
    #[OA\Response(response: 404, description: 'Not found', fan: \OpenApi\Spec\Fan::All)]
    public function show(): void
    {
    }

    #[OA\Get(path: '/accounts', responses: [new OA\Response(response: 200, description: 'OK')])]
    #[OA\Delete(path: '/accounts', responses: [new OA\Response(response: 204, description: 'Gone')])]
    #[OA\QueryParameter(name: 'q', fan: \OpenApi\Spec\Fan::All)]
    public function search(#[OA\QueryParameter(name: 'page', fan: \OpenApi\Spec\Fan::All)] int $page): void
    {
    }

    #[OA\Get(path: '/limits')]
    #[OA\Response(response: 200, description: 'OK')]
    #[OA\Response(response: 429, description: 'Slow down')]
    #[OA\Header(header: 'X-Rate-Limit', description: 'Requests left', fan: \OpenApi\Spec\Fan::All)]
    #[OA\Link(link: 'self', operationId: 'limits', fan: \OpenApi\Spec\Fan::All)]
    public function limits(): void
    {
    }

    #[OA\Post(path: '/upload', responses: [new OA\Response(response: 200, description: 'OK')])]
    #[OA\Put(path: '/upload', responses: [new OA\Response(response: 200, description: 'OK')])]
    #[OA\RequestBody(description: 'The file', fan: \OpenApi\Spec\Fan::All)]
    public function upload(): void
    {
    }

    #[OA\Get(path: '/filter', responses: [new OA\Response(response: 200, description: 'OK')])]
    #[OA\QueryParameter(name: 'from')]
    #[OA\QueryParameter(name: 'to')]
    #[OA\Examples(example: 'today', summary: 'Today', value: 'today', fan: \OpenApi\Spec\Fan::All)]
    public function filter(): void
    {
    }
}

?>
