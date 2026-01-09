<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Schema()]
class NestedProperty
{
    /**
     * @var bool
     */
    #[OAT\Property(
        properties: [
            new OAT\Property(
                property: 'babyProperty',
                properties: [
                    new OAT\Property(
                        property: 'theBabyOfBaby',
                        properties: [
                            new OAT\Property(property: 'theBabyOfBabyBaby', type: 'string'),
                        ],
                    ),
                ],
            ),
        ],
    )]
    public $parentProperty;
}
