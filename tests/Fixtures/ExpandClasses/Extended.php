<?php

namespace OpenApi\Tests\Fixtures\ExpandClasses;

/**
 *  @OA\Schema(
 *   schema="ExtendedModel",
 *   allOf={
 *      @OA\Schema(ref="#/components/schemas/Base"),
 *   }
 * )
 */
class Extended extends Base
{

    /**
     * @OA\Property();
     * @var string
     */
    public $extendedProperty;
}
