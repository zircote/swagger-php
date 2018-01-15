<?php

namespace petstore;

class PetsController
{

    /**
     * @OAS\Get(
     *     path="/pets",
     *     summary="List all pets",
     *     operationId="listPets",
     *     tags={"pets"},
     *     @OAS\Parameter(
     *         name="limit",
     *         in="query",
     *         description="How many items to return at one time (max 100)",
     *         required=false,
     *         type="integer",
     *         format="int32"
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="An paged array of pets",
     *         @OAS\Schema(ref="#/definitions/Pets"),
     *         @OAS\Header(header="x-next", type="string", description="A link to the next page of responses")
     *     ),
     *     @OAS\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OAS\Schema(
     *             ref="#/definitions/Error"
     *         )
     *     )
     * )
     */
    public function listPets()
    {
    }

    /**
     * @OAS\Post(
     *    path="/pets",
     *    summary="Create a pet",
     *    operationId="createPets",
     *    tags={"pets"},
     *    @OAS\Response(response=201, description="Null response"),
     *    @OAS\Response(
     *        response="default",
     *        description="unexpected error",
     *        @OAS\Schema(ref="#/definitions/Error")
     *    )
     * )
     */
    public function createPets()
    {
    }

    /**
     * @OAS\Get(
     *     path="/pets/{petId}",
     *     summary="Info for a specific pet",
     *     operationId="showPetById",
     *     tags={"pets"},
     *     @OAS\Parameter(
     *         name="petId",
     *         in="path",
     *         required=true,
     *         description="The id of the pet to retrieve",
     *         type="string"
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="Expected response to a valid request",
     *         @OAS\Schema(ref="#/definitions/Pets")
     *     ),
     *     @OAS\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OAS\Schema(ref="#/definitions/Error")
     *     )
     * )
     */
    public function showPetById($id)
    {
    }
}
