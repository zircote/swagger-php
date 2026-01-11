<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

use OpenApi\Attributes as OAT;

#[OAT\Schema(properties: [new OAT\Property(property: 'nested', ref: '#/components/schemas/NestedSchema')])]
#[OAT\Schema(schema: 'NestedSchema', properties: [new OAT\Property(property: 'nestedProperty', type: 'string')])]
class ExtendedWithTwoSchemas extends Base
{
    /**
     * @var string
     */
    #[OAT\Property]
    public $extendedProperty;
}
