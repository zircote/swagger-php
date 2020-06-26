<?php

namespace UsingTraits;

/**
 * @OA\Schema(title="Delete entity trait")
 *
 * @todo Not sure if this is correct or wanted behaviour...
 */
trait DeleteEntity {

    /**
     * @OA\Delete(
     *   tags={"Entities"},
     *   path="/entities/{id}",
     *   description="Delete entity",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Entity id",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="successful operation"
     *   )
     * )
     */
    public function deleteEntity($id)
    {
    }
}
