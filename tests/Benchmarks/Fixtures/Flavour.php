<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Fixtures;

/**
 * The attributes a generated API is written with: classic's `OpenApi\Attributes`, read by the
 * classic and hybrid modes, or `OpenApi\Spec`, read by spec mode. A spec tree is the classic one
 * migrated with the rector set.
 */
enum Flavour: string
{
    case Classic = 'Classic';
    case Spec = 'Spec';
}
