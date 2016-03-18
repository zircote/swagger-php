<?php

namespace AnotherNamespace;

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
     *             @SWG\Property(type="string", property="theBabyOfBabyBaby")
     *         )
     *     ),
     * )
     */
    public $parentProperty;
}
