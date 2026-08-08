<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

trait HasIdExtendedSpec
{
    #[OA\Property]
    #[OA\Schema(
        format: 'int64',
        readOnly: true,
    )]
    public int $id;
}

trait HasTimestampsExtendedSpec
{
    #[OA\Property]
    #[OA\Schema(
        format: 'date-time',
        type: 'string',
        readOnly: true,
    )]
    public \DateTime $created_at;

    #[OA\Property]
    #[OA\Schema(
        format: 'date-time',
        type: 'string',
        readOnly: true,
    )]
    public \DateTime $updated_at;
}

trait HasSoftDeleteExtendedSpec
{
    #[OA\Property]
    #[OA\Schema(
        format: 'date-time',
        type: 'string',
        readOnly: true,
    )]
    public ?\DateTime $deleted_at;
}

#[OA\Schema(
    schema: 'ModelExtended',
    description: 'This model can be ignored, it is just used for inheritance.',
)]
/**
 * @see BaseModelSpec
 */
abstract class ModelExtendedSpec
{
    use HasIdExtendedSpec;
    use HasTimestampsExtendedSpec;
}

#[OA\Schema(
    schema: 'Product',
    description: 'Product',
    required: ['number', 'name'],
    xml: new OA\Xml(name: 'Product'),
)]
/**
 * @see ProductModelSpec
 */
class ProductSpec extends ModelExtendedSpec
{
    use HasSoftDeleteExtendedSpec;

    #[OA\Property]
    public string $number;

    #[OA\Property]
    public string $name;
}

#[OA\Info(title: 'API', version: '1.0')]
#[OA\Operation\Get(
    path: '/api/endpoint',
    operationId: 'getProducts',
)]
#[OA\Response(
    response: 200,
    description: 'successful operation',
    content: new OA\MediaType\Json(ref: ProductSpec::class),
)]
class EndpointExtendedSpec
{
}
