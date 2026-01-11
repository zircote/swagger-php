<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Schema()]
class TypedProperties
{
    #[OAT\Property]
    public string $stringType;

    #[OAT\Property]
    public int $intType;

    #[OAT\Property]
    public ?string $nullableString;

    /**
     * @var TypedProperties[]
     */
    #[OAT\Property]
    public array $arrayType;

    #[OAT\Property]
    public \DateTime $dateTime;

    #[OAT\Property(type: 'integer')]
    public \DateTime $dateTimeTimestamp;

    #[OAT\Property]
    public \DateTimeInterface $qualified;

    #[OAT\Property]
    public TypedProperties $namespaced;

    #[OAT\Property]
    public TypedProperties $importedNamespace;

    /**
     * @var int
     */
    #[OAT\Property]
    public string $varTrumpsNative;

    /**
     * @var bool
     */
    #[OAT\Property(type: 'integer')]
    public string $annotationTrumpsNative;

    #[OAT\Property(type: 'integer')]
    public string $annotationTrumpsAll;

    #[OAT\Property]
    public $undefined;

    #[OAT\Property(type: 'integer')]
    public $onlyAnnotated;

    /**
     * @var int
     */
    #[OAT\Property]
    public $onlyVar;

    #[OAT\Property]
    public static $staticUndefined;

    #[OAT\Property]
    public static string $staticString;

    #[OAT\Property]
    public static ?string $staticNullableString;

    /**
     * @var string[]
     */
    #[OAT\Property]
    public array $nativeArray;
}
