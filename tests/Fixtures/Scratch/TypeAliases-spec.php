<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Type aliases', version: '1.0')]
#[OA\Operation\Get(path: '/trip', operationId: 'getTrip')]
#[OA\Response(response: 200, description: 'OK', content: new OA\MediaType\Json(schema: new OA\Schema(ref: '#/components/schemas/TypeAliasesTrip')))]
class TypeAliasesEndpointSpec
{
}

// the names classic maps through its native type map, written as an explicit `type`
#[OA\Schema(component: 'TypeAliasesTrip')]
class TypeAliasesTripSpec
{
    #[OA\Property(schema: new OA\Schema(type: 'long'))]
    public $id;

    #[OA\Property(schema: new OA\Schema(type: 'int'))]
    public $stops;

    #[OA\Property(schema: new OA\Schema(type: 'bool'))]
    public $return;

    #[OA\Property(schema: new OA\Schema(type: 'float'))]
    public $distance;

    #[OA\Property(schema: new OA\Schema(type: 'double', format: 'decimal'))]
    public $fare;

    #[OA\Property(schema: new OA\Schema(type: 'date'))]
    public $departure;

    #[OA\Property(schema: new OA\Schema(type: 'DateTime'))]
    public $booked;
}
