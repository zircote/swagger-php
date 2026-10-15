<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Type aliases', version: '1.0')]
#[OAT\Get(
    path: '/trip',
    operationId: 'getTrip',
    responses: [new OAT\Response(response: 200, description: 'OK', content: new OAT\JsonContent(ref: '#/components/schemas/TypeAliasesTrip'))],
)]
class TypeAliasesEndpoint
{
}

// the names classic maps through its native type map, written as an explicit `type`
#[OAT\Schema(schema: 'TypeAliasesTrip')]
class TypeAliasesTrip
{
    #[OAT\Property(type: 'long')]
    public $id;

    #[OAT\Property(type: 'int')]
    public $stops;

    #[OAT\Property(type: 'bool')]
    public $return;

    #[OAT\Property(type: 'float')]
    public $distance;

    #[OAT\Property(type: 'double', format: 'decimal')]
    public $fare;

    #[OAT\Property(type: 'date')]
    public $departure;

    #[OAT\Property(type: 'DateTime')]
    public $booked;
}
