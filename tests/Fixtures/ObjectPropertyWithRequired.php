<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class ObjectPropertyWithRequired
{
    #[OAT\Property(
        property: 'address',
        properties: [
            new OAT\Property(property: 'street', type: 'string'),
        ],
        required: ['street'],
    )]
    public $address;
}
