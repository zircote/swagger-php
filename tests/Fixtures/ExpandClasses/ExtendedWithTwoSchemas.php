<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

/**
 * @OA\Schema(@OA\Property(property="nested",ref="#/components/schemas/NestedSchema")),
 * @OA\Schema(schema="NestedSchema", @OA\Property(property="nestedProperty", type="string"))
 */
class ExtendedWithTwoSchemas extends Base
{

    /**
     * @OA\Property();
     * @var string
     */
    public $extendedProperty;
}
