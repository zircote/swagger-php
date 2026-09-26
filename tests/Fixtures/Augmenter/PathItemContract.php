<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

/**
 * An interface carrying a PathItem. The hierarchy walk follows parent classes only, so this
 * never governs the classes implementing it.
 */
#[OA\PathItem(prefix: '/contract', tags: ['Contract'])]
interface PathItemContract
{
}
