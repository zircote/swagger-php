<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'Checkout')]
class Checkout
{
    #[OA\Property(property: 'payment')]
    public Payment $payment;
}
