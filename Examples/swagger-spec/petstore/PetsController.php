<?php

namespace petstore;

class PetsController
{

    /**
     * @SWG\Get(
     *     path="/pets",
     *     summary="List all pets",
     *     operationId="listPets",
     *     tags={"pets"},
     *     @SWG\Parameter(
     *         name="limit",
     *         in="query",
     *         description="How many items to return at one time (max 100)",
     *         required=false,
     *         type="integer",
     *         format="int32"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="An paged array of pets",
     *         @SWG\Schema(ref="#/definitions/Pets"),
     *         @SWG\Header(header="x-next", type="string", description="A link to the next page of responses")
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/Error"
     *         )
     *     )
     * )
     */
    public function listPets()
    {
    }

    /**
     * @SWG\Post(
     *    path="/pets",
     *    summary="Create a pet",
     *    operationId="createPets",
     *    tags={"pets"},
     *    @SWG\Response(response=201, description="Null response"),
     *    @SWG\Response(
     *        response="default",
     *        description="unexpected error",
     *        @SWG\Schema(ref="#/definitions/Error")
     *    )
     * )
     */
    public function createPets()
    {
    }

    /**
     * @SWG\Get(
     *     path="/pets/{petId}",
     *     summary="Info for a specific pet",
     *     operationId="showPetById",
     *     tags={"pets"},
     *     @SWG\Parameter(
     *         name="petId",
     *         in="path",
     *         required=true,
     *         description="The id of the pet to retrieve",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Expected response to a valid request",
     *         @SWG\Schema(ref="#/definitions/Pets")
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(ref="#/definitions/Error")
     *     )
     * )
     */
    public function showPetById($id)
    {
    }
}
