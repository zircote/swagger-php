<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema]
class GenericsCustomerSpec
{
    #[OA\Property]
    public int $id;
}

/**
 * A generic class gets a schema per set of type arguments it is used with.
 *
 * @template T
 */
#[OA\Schema]
class GenericsEnvelopeSpec
{
    /** @var T */
    #[OA\Property]
    public mixed $data;

    /** @var list<T> */
    #[OA\Property]
    public array $history;

    /** @var T|null */
    #[OA\Property]
    public mixed $previous;

    // the class's own template as an argument is the plain schema here, and the copy's own in a copy
    /** @var GenericsEnvelopeSpec<T>|null */
    #[OA\Property]
    public ?GenericsEnvelopeSpec $parent;

    #[OA\Property]
    public string $version;
}

/**
 * A copy of a schema with a name of its own builds on that name.
 *
 * @template K
 * @template V
 */
#[OA\Schema(component: 'GenericsPair')]
class GenericsPairSpec
{
    /** @var K */
    #[OA\Property]
    public mixed $key;

    // `mixed` for `K` makes this `mixed` too
    /** @var K|int */
    #[OA\Property]
    public mixed $keyOrIndex;

    /** @var V */
    #[OA\Property]
    public mixed $value;
}

enum GenericsStatusSpec: string
{
    case Open = 'open';
    case Closed = 'closed';
}

#[OA\Schema]
class GenericsHolderSpec
{
    /** @var GenericsEnvelopeSpec<GenericsCustomerSpec> */
    #[OA\Property]
    public GenericsEnvelopeSpec $customer;

    /** @var GenericsEnvelopeSpec<int> */
    #[OA\Property]
    public GenericsEnvelopeSpec $number;

    /** @var GenericsEnvelopeSpec<GenericsEnvelopeSpec<GenericsCustomerSpec>> */
    #[OA\Property]
    public GenericsEnvelopeSpec $nested;

    /** @var list<GenericsPairSpec<string, GenericsCustomerSpec>> */
    #[OA\Property]
    public array $pairs;

    // `mixed` says nothing the template does not, so this is the plain schema
    /** @var GenericsEnvelopeSpec<mixed> */
    #[OA\Property]
    public GenericsEnvelopeSpec $anything;

    /** @var GenericsPairSpec<mixed, GenericsCustomerSpec> */
    #[OA\Property]
    public GenericsPairSpec $loose;

    // a nested list followed by another argument is closed, so `int` reads as the pair's
    /** @var GenericsPairSpec<GenericsEnvelopeSpec<GenericsCustomerSpec>, int> */
    #[OA\Property]
    public GenericsPairSpec $wrapped;

    /** @var GenericsEnvelopeSpec<GenericsCustomerSpec|int> */
    #[OA\Property]
    public GenericsEnvelopeSpec $either;

    // an enum only an argument names still gets its schema
    /** @var GenericsEnvelopeSpec<GenericsStatusSpec> */
    #[OA\Property]
    public GenericsEnvelopeSpec $status;

    // a ref written out asks for the same schema as a type
    #[OA\Property(schema: new OA\Schema\Ref(ref: GenericsEnvelopeSpec::class, qualifiers: ['of' => 'bool']))]
    public GenericsEnvelopeSpec $flag;
}

#[OA\Info(title: 'Generics', version: '1.0')]
#[OA\Operation\Get(path: '/holder', operationId: 'getHolder')]
#[OA\Response(response: 200, description: 'All good', content: [
    new OA\MediaType\Json(ref: GenericsHolderSpec::class),
])]
class GenericsEndpointSpec
{
}
