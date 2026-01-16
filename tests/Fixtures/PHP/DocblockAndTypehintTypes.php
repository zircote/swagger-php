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
     * @var string|null
     */
    #[OAT\Property(nullable: false)]
    public ?string $nullableStringExplicit;

    /**
     * @var string|null
     */
    #[OAT\Property()]
    public mixed $nullableStringDocblock;

    #[OAT\Property()]
    public ?string $nullableStringNative;

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
     * @var array<string>
     */
    #[OAT\Property(items: new OAT\Items(example: 'foo'))]
    public array $stringListExplicit;

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
        private OAT\Tag     $tag,
        #[OAT\Property]
        protected string    $promotedString,
        bool                $bool = true,
        #[OAT\Property(example: 'My value')]
        public string|array $mixedUnion = [],
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

    /**
     * @var DocblockAndTypehintTypes
     */
    #[OAT\Property(
        oneOf: [
            new OAT\Schema(type: 'string'),
            new OAT\Schema(type: 'bool'),
        ]
    )]
    public $oneOfVar;

    /**
     * @var array<DocblockAndTypehintTypes>
     */
    #[OAT\Property(
        items: new OAT\Items(oneOf: [
            new OAT\Schema(type: 'string'),
            new OAT\Schema(type: 'bool'),
        ])
    )]
    public array $oneOfList;

    /**
     * @param \DateTimeImmutable[] $paramDateTimeList
     * @param string[] $paramStringList
     */
    public function paramMethod(
        #[OAT\Property]
        array $paramDateTimeList,
        #[OAT\Property]
        array $paramStringList,
    ): void
        {
        }

    /**
     * @param ?string[] $blah_values
     */
    public function blah(
        #[OAT\Property(example: 'My blah')]
        ?string $blah,
        #[OAT\Property(nullable: true, items: new OAT\Items(type: 'string', example: 'hello'))]
        ?array  $blah_values,
    ) {
    }
<<<<<<< HEAD
=======

    #[OAT\Property]
    public FirstInterface&SecondInterface $intersectionVar;

    /**
     * @var array<DocblockAndTypehintTypes>|array<string>
     */
    #[OAT\Property]
    public array $nestedOneOf;

    /**
     * @var array<DocblockAndTypehintTypes>|array<string>
     */
    #[OAT\Property(items: new OAT\Items(oneOf: [
        new OAT\Schema(type: DocblockAndTypehintTypes::class),
        new OAT\Schema(type: 'string'),
    ]))]
    public array $nestedOneOfWithItems;
>>>>>>> 0e20d17 (Skip type resolving on `Schema` with `items` set (#1916) the )
}
