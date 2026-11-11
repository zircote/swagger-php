<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

// a generic class gets a schema per set of type arguments it is used with; hybrid only, classic
// references the plain schema
#[OAT\Schema]
class GenericsCustomer
{
    #[OAT\Property]
    public int $id;
}

/**
 * @template T
 */
#[OAT\Schema]
class GenericsEnvelope
{
    /** @var T */
    #[OAT\Property]
    public mixed $data;

    /** @var list<T> */
    #[OAT\Property]
    public array $history;

    #[OAT\Property]
    public string $version;
}

#[OAT\Schema]
class GenericsHolder
{
    /** @var GenericsEnvelope<GenericsCustomer> */
    #[OAT\Property]
    public GenericsEnvelope $customer;

    /** @var GenericsEnvelope<int> */
    #[OAT\Property]
    public GenericsEnvelope $number;
}

#[OAT\Info(title: 'Generics', version: '1.0')]
class GenericsEndpoint
{
    #[OAT\Get(
        path: '/holder',
        operationId: 'getHolder',
        responses: [
            new OAT\Response(
                response: 200,
                description: 'All good',
                content: new OAT\JsonContent(ref: GenericsHolder::class)
            ),
        ]
    )]
    public function holder(): void
    {
    }
}
