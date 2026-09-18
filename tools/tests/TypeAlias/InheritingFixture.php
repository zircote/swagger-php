<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Tests\TypeAlias;

/**
 * @phpstan-import-type EnumValue from DeclaringFixture
 */
trait AliasDeclaringTrait
{
    /**
     * @var list<EnumValue>
     */
    public array $enum = [];
}

/**
 * Uses the trait but declares nothing itself, the shape `Annotations\Items` has: the `@var`
 * being expanded lives in the trait, while reflection names this class as the declaring one.
 */
class InheritingFixture
{
    use AliasDeclaringTrait;
}
