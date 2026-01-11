<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'ExtendedModel',
    allOf: [new OAT\Schema(ref: '#/components/schemas/Base')]
)]
class Extended extends Base
{
    /**
     * @var string
     */
    #[OAT\Property]
    public $extendedProperty;
}
