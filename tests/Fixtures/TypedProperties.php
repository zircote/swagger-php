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
     * @OA\Property()
     * @var int
     */
    public string $nativeTrumpsVar;
}
