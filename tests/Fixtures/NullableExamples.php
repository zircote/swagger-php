<?php

namespace OpenApiTests\Fixtures;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Example for nullable variables with example"
 * )
 */

/**
 * @OA\Schema()
 */
class NullableExamples
{
    /**
     * @OA\Property(
     *   nullable=true,
     * )
     */
    public $variable;

    /**
     * @OA\Property(example=null)
     */
    public $notNullable;

    /**
     * @OA\Property(
     *   nullable=true,
     *   example="hello"
     * )
     */
    public $nullableWithExample;
}
