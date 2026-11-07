<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'Bin')]
class Bin
{
    // the trait's property is merged by `Inheritance\Schemas`, after the resolver has run
    use Stocked;
}
