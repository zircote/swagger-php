<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Attributes as OAT;

/**
 * Classic type aliases in properties nested in a class's schema, one of them in the items of another.
 */
#[OAT\Schema(
    schema: 'NestedTypeAliases',
    properties: [
        new OAT\Property(property: 'price', type: 'float'),
        new OAT\Property(property: 'counts', type: 'array', items: new OAT\Items(type: 'int')),
    ],
)]
class NestedTypeAliases
{
    #[OAT\Property(type: 'double')]
    public $total;
}
