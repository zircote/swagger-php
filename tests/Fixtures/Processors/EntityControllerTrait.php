<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Processors;

/**
 * Entity controller trait.
 */
trait EntityControllerTrait
{
    /**
     * @OA\Delete(
     *     tags={"EntityController"},
     *     path="entities/{id}",
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     )
     * )
     */
    public function deleteEntity($id)
    {
    }
}
