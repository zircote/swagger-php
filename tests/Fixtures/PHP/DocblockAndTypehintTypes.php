<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Attributes as OAT;

#[OAT\Schema()]
class DocblockAndTypehintTypes
{
    public $nothing;

    /**
     * @var string $string
     */
    public string $string;

    /**
     * @var string|null
     */
    public ?string $nullableString;

    /**
     * @var string[]
     */
    public array $stringArray;

    /**
     * @var array<string>
     */
    public array $stringList;

    /**
     * @var ?array<string>
     */
    public ?array $nullableStringList;

    /**
     * @var array<string>|null
     */
    public array|null $nullableStringListUnion;

    /**
     * @var DocblockAndTypehintTypes
     */
    public DocblockAndTypehintTypes $class;

    /**
     * @var DocblockAndTypehintTypes|null
     */
    public ?DocblockAndTypehintTypes $nullableClass;

    /**
     * @var \DateTime
     */
    public \DateTime $namespacedGlobalClass;

    /**
     * @var \DateTime|null
     */
    public \DateTime|null $nullableNamespacedGlobalClass;

    /**
     * @var \DateTime|null
     */
    public null|\DateTime $alsoNullableNamespacedGlobalClass;

    /**
     * @var int<min,10> An int range
     */
    public int $intRange;

    /**
     * @var positive-int The positive integer
     */
    public int $positiveInt;

    /**
     * @var non-zero-int The non-zero integer
     */
    public int $nonZeroInt;

    /**
     * @var array{foo:bool}
     */
    public array $arrayShape;

    /**
     * @var int|string
     */
    public int|string $unionType;

    /**
     * @param OAT\Tag $tag
     * @param string $promotedString
     * @param bool $bool
     */
    public function __construct(
        private OAT\Tag $tag,
        protected string $promotedString,
        bool $bool = true
    )
    {
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return 'string';
    }
}
