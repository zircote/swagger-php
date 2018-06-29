<?php

/**
 * @license Apache 2.0
 */

/**
 *
 * @OA\RequestBody(
 *     request="Pet",
 *     description="Pet object that needs to be added to the store",
 *     required=true,
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             ref="#/components/schemas/Pet"
 *         )
 *     ),
 *     @OA\MediaType(
 *         mediaType="application/xml",
 *         @OA\Schema(
 *             ref="#/components/schemas/Pet"
 *         )
 *     )
 * )
 */

/**
 * @OA\RequestBody(
 *     request="UserArray",
 *     description="List of user object",
 *     required=true,
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(
 *                 ref="#/components/schemas/User"
 *             )
 *         )
 *     )
 * )
 */
