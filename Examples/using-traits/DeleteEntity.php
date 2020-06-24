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
     *   @OA\Parameter(
     *     name="id",
     *     in="query",
     *     description="Entity id",
     *     required=false,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *       response="default",
     *       description="successful operation"
     *   )
     * )
     */
    public function deleteEntity($id)
    {
    }
}
