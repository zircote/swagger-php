<?php declare(strict_types=1);

namespace AnotherNamespace;

/**
 * @OAS\Schema()
 */
class NestedProperty
{

    /**
     * @var bool
     * @OAS\Property(
     *     @OAS\Property(
     *         property="babyProperty",
     *         @OAS\Property(
     *             property="theBabyOfBaby",
     *             properties={@Swagger\Annotations\Property(type="string", property="theBabyOfBabyBaby")}
     *         )
     *     ),
     * )
     */
    public $parentProperty;
}
