<?php

/**
 * @OA\Schema(
 *      schema="StringList",
 *      @OA\Property(property="value", type="array", @OA\Items(anyOf={@OA\Schema(type="string")}))
 * )
 * @OA\Schema(
 *      schema="String",
 *      @OA\Property(property="value", type="string")
 * )
 * @OA\Schema(
 *      schema="Object",
 *      @OA\Property(property="value", type="object")
 * )
 * @OA\Schema(
 *     schema="mixedList",
 *     @OA\Property(property="fields", type="array", @OA\Items(oneOf={
 *         @OA\Schema(ref="#/components/schemas/StringList"),
 *         @OA\Schema(ref="#/components/schemas/String"),
 *         @OA\Schema(ref="#/components/schemas/Object")
 *     }))
 * )
 */
class OpenApiSpec
{
}
