<?php

namespace OpenApi\Tests\Fixtures\InheritProperties;

/**
 * @OA\Schema(
 *     excludeProperties={"firstProperty"}
 * )
 */
class ExcludedProperties
{

    /**
     * @OA\Property();
     * @var string
     */
    public $firstProperty;

    /**
     * @OA\Property();
     * @var string
     */
    public $secondProperty;
}
