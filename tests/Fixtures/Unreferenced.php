<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(version="1", title="foo")
 * @OA\Schema(
 *     schema="ExampleSchema",
 *     type="object",
 * 		@OA\Property(
 *         property="layouts",
 *         type="array",
 *    		@OA\Items(
 *             type="object",
 *     			@OA\Property(
 *                 property="desktop",
 *                 ref="#/components/schemas/ExampleNestedSchema"
 *             )
 *         )
 *     )
 * )
 * @OA\Schema(
 *     schema="ExampleNestedSchema",
 *     type="object",
 *     @OA\Property(
 *         property="rows",
 *         type="array",
 *     		@OA\Items(
 *             type="object",
 *         )
 *     )
 * )
 */
class Unreferenced
{
    /**
     * @OA\PathItem(
     *     path="/path",
     * 		@OA\Parameter(
     *         name="example_param",
     *         in="query",
     *         style="spaceDelimited",
     *         explode=true,
     *         required=true
     *     )
     * )
     */
    public function someEndpoint()
    {
    }
}
