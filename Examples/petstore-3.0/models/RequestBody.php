<?php

/**
 * @license Apache 2.0
 */

/**
 *
 * @OAS\RequestBody(
 *     request="Pet",
 *     description="Pet object that needs to be added to the store",
 *     required=true,
 *     @OAS\MediaType(
 *         mediaType="application/json",
 *         @OAS\Schema(
 *             ref="#/components/schemas/Pet"
 *         )
 *     ),
 *     @OAS\MediaType(
 *         mediaType="application/xml",
 *         @OAS\Schema(
 *             ref="#/components/schemas/Pet"
 *         )
 *     )
 * )
 */

/**
 * @OAS\RequestBody(
 *     request="UserArray",
 *     description="List of user object",
 *     required=true,
 *     @OAS\MediaType(
 *         mediaType="application/json",
 *         @OAS\Schema(
 *             type="array",
 *             @OAS\Items(
 *                 ref="#/components/schemas/User"
 *             )
 *         )
 *     )
 * )
 */
