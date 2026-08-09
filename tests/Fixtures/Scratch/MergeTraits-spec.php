<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

trait HasIdSpec
{
    #[OA\Property]
    #[OA\Schema(format: 'int64', readOnly: true)]
    public int $id;
}

trait HasTimestampsSpec
{
    #[OA\Property]
    #[OA\Schema(format: 'date-time', type: 'string', readOnly: true)]
    public \DateTime $created_at;

    #[OA\Property]
    #[OA\Schema(format: 'date-time', type: 'string', readOnly: true)]
    public \DateTime $updated_at;
}

abstract class ModelSpec
{
    use HasIdSpec;
}

#[OA\Schema(
    schema: 'Address',
    required: ['street'],
    xml: new OA\Xml(name: 'Address'),
)]
class AddressSpec extends ModelSpec
{
    use HasTimestampsSpec;

    #[OA\Property]
    public string $street;
}

#[OA\Info(title: 'API', version: '1.0')]
#[OA\Operation\Get(
    path: '/api/endpoint',
    operationId: 'getMergedAddress',
)]
#[OA\Response(
    response: 200,
    description: 'successful operation',
    content: new OA\MediaType\Json(ref: AddressSpec::class)
)]
class MergeTraitsEndpointSpec
{
}
