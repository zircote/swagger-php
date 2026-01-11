<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

trait HasIdExtended
{
    #[OAT\Property(
        format: 'int64',
        readOnly: true,
    )]
    public int $id;
}

trait HasTimestampsExtended
{
    #[OAT\Property(
        format: 'date-time',
        type: 'string',
        readOnly: true,
    )]
    public \DateTime $created_at;

    #[OAT\Property(
        format: 'date-time',
        type: 'string',
        readOnly: true,
    )]
    public \DateTime $updated_at;
}

trait HasSoftDeleteExtended
{
    #[OAT\Property(
        format: 'date-time',
        type: 'string',
        readOnly: true,
    )]
    public ?\DateTime $deleted_at;
}

#[OAT\Schema(description: 'This model can be ignored, it is just used for inheritance.')]
/**
 * @see BaseModel
 */
abstract class ModelExtended
{
    use HasIdExtended;
    use HasTimestampsExtended;
}

#[OAT\Schema(
    description: 'Product',
    required: ['number', 'name'],
    xml: new OAT\Xml(name: 'Product'),
)]
/**
 * @see ProductModel
 */
class Product extends ModelExtended
{
    use HasSoftDeleteExtended;

    #[OAT\Property]
    public string $number;

    #[OAT\Property]
    public string $name;
}

#[OAT\Info(title: 'API', version: '1.0')]
#[OAT\Get(path: '/api/endpoint')]
#[OAT\Response(
    response: 200,
    description: 'successful operation',
    content: new OAT\JsonContent(ref: Product::class),
)]
class EndpointExtended
{
}
