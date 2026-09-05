<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Tests\Fixtures\PHP\Inheritance\BaseClass as ParentClass;
use OpenApi\Tests\Fixtures\PHP\Inheritance\BaseInterface as Contract;

trait AlphaTrait
{
}

trait BetaTrait
{
}

trait GammaTrait
{
}

/**
 * A trait composed of other traits, one `use` statement each.
 */
trait CombinedTrait
{
    use AlphaTrait;
    use BetaTrait;
}

/**
 * Aliased imports standing in for the parent and the interface, and two traits pulled in
 * by a single comma-separated `use`.
 */
class TraitUsage extends ParentClass implements Contract
{
    use CombinedTrait, GammaTrait;

    public const CONSTANT = 'value';

    public string $name = '';

    public function method(): void
    {
    }
}
