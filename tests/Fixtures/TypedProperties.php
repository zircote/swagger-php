<?php
declare(strict_types=1);

namespace OpenApiFixures;

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
    public ?string $nullableString;
}
