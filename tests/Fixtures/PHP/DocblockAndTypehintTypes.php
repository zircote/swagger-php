<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Attributes as OAT;

#[OAT\Schema()]
class DocblockAndTypehintTypes
{
    #[OAT\Property]
    public $nothing;

    /**
     * @var string
     */
    #[OAT\Property]
    public string $string;

    /**
     * @var string|null
     */
    #[OAT\Property]
    public ?string $nullableString;

    /**
     * @var string[]
     */
    #[OAT\Property]
    public array $stringArray;

    /**
     * @var array<string>
     */
    #[OAT\Property]
    public array $stringList;

    /**
     * @var ?array<string>
     */
    #[OAT\Property]
    public ?array $nullableStringList;

    /**
     * @var array<string>|null
     */
    #[OAT\Property]
    public array|null $nullableStringListUnion;

    /**
     * @var DocblockAndTypehintTypes
     */
    #[OAT\Property]
    public DocblockAndTypehintTypes $class;

    /**
     * @var DocblockAndTypehintTypes|null
     */
    #[OAT\Property]
    public ?DocblockAndTypehintTypes $nullableClass;

    /**
     * @var \DateTime
     */
    #[OAT\Property]
    public \DateTime $namespacedGlobalClass;

    /**
     * @var \DateTime|null
     */
    #[OAT\Property]
    public \DateTime|null $nullableNamespacedGlobalClass;

    /**
     * @var \DateTime|null
     */
    #[OAT\Property]
    public null|\DateTime $alsoNullableNamespacedGlobalClass;

    /**
     * @var int<min,10> An int range
     */
    #[OAT\Property]
    public int $intRange;

    /**
     * @var positive-int The positive integer
     */
    #[OAT\Property]
    public int $positiveInt;

    /**
     * @var non-zero-int The non-zero integer
     */
    #[OAT\Property]
    public int $nonZeroInt;

    /**
     * @var array{foo:bool}
     */
    #[OAT\Property]
    public array $arrayShape;

    /**
     * @var int|string
     */
    #[OAT\Property]
    public int|string $unionType;

    /**
     * @param OAT\Tag $tag
     * @param string $promotedString
     * @param bool $bool
     */
    public function __construct(
        private OAT\Tag $tag,
        #[OAT\Property]
        protected string $promotedString,
        bool $bool = true
    )
    {
    }

    /**
     * @return string
     */
    #[OAT\Property]
    public function getString(): string
    {
        return 'string';
    }
}
