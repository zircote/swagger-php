<?php

namespace SwaggerFixures;

/**
 * @SWG\Definition()
 */
class NestedProperty
{

    /**
     * @var bool
     * @SWG\Property(
     *     @SWG\Property(
     *         property="babyProperty",
     *         @SWG\Property(
     *             property="theBabyOfBaby",
     *             properties={@Swagger\Annotations\Property(type="string", property="theBabyOfBabyBaby")}
     *         )
     *     ),
     * )
     */
    public $parentProperty;
}
