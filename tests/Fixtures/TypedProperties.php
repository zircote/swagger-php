<?php
declare(strict_types=1);

namespace OpenApiFixures;

use DateTime;

/**
 * @OA\Schema()
 */
class TypedProperties
{
    /**
     * @OA\Property()
     */
    public string $stringType;

    /**
     * @OA\Property()
     */
    public int $intType;

    /**
     * @OA\Property()
     */
    public ?string $nullableString;

    /**
     * @OA\Property()
     */
    public DateTime $dateTime;

    /**
     * @var int
     * @OA\Property()
     */
    public string $nativeTrumpsVar;

    /**
     * @var int
     * @OA\Property(
     *     type="int",
     * )
     */
    public string $annotationTrumpsNative;

    /**
     * @var string
     * @OA\Property(
     *     type="int",
     * )
     */
    public string $annotationTrumpsAll;

    /**
     * @OA\Property()
     */
    public $undefined;

    /**
     * @OA\Property(
     *     type="int",
     * )
     */
    public $onlyAnnotated;

    /**
     * @OA\Property()
     */
    public static $staticUndefined;

    /**
     * @OA\Property()
     */
    public static string $staticString;

    /**
     * @OA\Property()
     */
    public static ?string $staticNullableString;
}
