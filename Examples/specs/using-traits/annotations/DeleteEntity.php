<?php

namespace OpenApi\Examples\Specs\UsingTraits\Annotations;

use OpenApi\Annotations as OA;

trait DeleteEntity
{
    /**
     * @OA\Delete(
     *     tags={"Entities"},
     *     path="/entities/{id}",
     *     operationId="deleteEntity",
     *     @OA\Parameter(
     *         description="ID of entity to delete",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
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
