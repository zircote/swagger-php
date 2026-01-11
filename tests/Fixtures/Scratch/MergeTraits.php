<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

trait HasId
{
    #[OAT\Property(format: 'int64', readOnly: true)]
    public int $id;
}

trait HasTimestamps
{
    #[OAT\Property(format: 'date-time', type: 'string', readOnly: true)]
    public \DateTime $created_at;

    #[OAT\Property(format: 'date-time', type: 'string', readOnly: true)]
    public \DateTime $updated_at;
}

abstract class Model
{
    use HasId;
}

#[OAT\Schema(
    required: ['street'],
    xml: new OAT\Xml(name: 'Address'),
)]
class Address extends Model
{
    use HasTimestamps;

    #[OAT\Property]
    public string $street;
}

#[OAT\Info(title: 'API', version: '1.0')]
#[OAT\Get(path: '/api/endpoint')]
#[OAT\Response(
    response: 200,
    description: 'successful operation',
    content: new OAT\JsonContent(ref: Address::class)
)]
class MergeTraitsEndpoint
{
}
