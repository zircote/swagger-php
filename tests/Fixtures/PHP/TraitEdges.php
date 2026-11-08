<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Attributes as OAT;

trait EdgeInner
{
    public const INNER = 'inner';

    public string $inner = '';

    public function inner(): void
    {
    }
}

trait EdgeOuter
{
    use EdgeInner;

    public const OUTER = 'outer';

    public const REDECLARED = 'redeclared';

    /**
     * The trait's words.
     */
    public string $documented = '';

    public string $redeclared = '';

    public string $redeclaredWithDoc = '';

    #[OAT\Property]
    public string $redeclaredWithAttribute = '';

    #[OAT\Property]
    public string $redeclaredWithOtherAttribute = '';

    public static int $counter = 0;

    #[OAT\Property]
    #[Unloadable(Unloadable::VALUE)]
    public string $foreign = '';

    public function __construct(public string $promoted = '')
    {
    }

    public function aliased(): void
    {
    }

    public function conflicting(): void
    {
    }

    public function overridden(): void
    {
    }

    abstract public function implemented(): void;

    public static function shared(): void
    {
    }
}

trait EdgeOther
{
    public function conflicting(): void
    {
    }
}

trait EdgeLabel
{
    public const LABEL = 'label';

    public function label(): string
    {
        return self::LABEL;
    }
}

/**
 * The ways PHP lets a class take members from a trait, and redeclare them.
 */
class TraitEdges
{
    use EdgeOuter, EdgeOther {
        EdgeOuter::conflicting insteadof EdgeOther;
        EdgeOther::conflicting as otherConflicting;
        aliased as protected renamed;
    }

    public const REDECLARED = 'redeclared';

    public string $redeclared = '';

    /**
     * The class's own words.
     */
    public string $redeclaredWithDoc = '';

    #[OAT\Property]
    public string $redeclaredWithAttribute = '';

    #[OAT\Property(description: 'the class')]
    public string $redeclaredWithOtherAttribute = '';

    public function overridden(): void
    {
    }

    public function implemented(): void
    {
    }
}

enum EdgeEnum
{
    use EdgeLabel;

    case ONE;
}

// a trait and a class on one line share their lines
trait EdgeOneLine { public function oneLine(): void {} } class EdgeOnOneLine { use EdgeOneLine; }
