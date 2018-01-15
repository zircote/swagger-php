<?php

/**
 *
 */
class PetWithDocsController
{

    /**
     * @OAS\Post(
     *     path="/pets",
     *     operationId="addPet",
     *     description="Creates a new pet in the store.  Duplicates are allowed",
     *     produces={"application/json"},
     *     @OAS\Parameter(
     *         name="pet",
     *         in="body",
     *         description="Pet to add to the store",
     *         required=true,
     *         @OAS\Schema(ref="#/definitions/NewPet"),
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="pet response",
     *         @OAS\Schema(ref="#/definitions/Pet")
     *     ),
     *     @OAS\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OAS\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function addPet()
    {
    }

    /**
     * @OAS\Get(
     *     path="/pets/{id}",
     *     description="Returns a user based on a single ID, if the user does not have access to the pet",
     *     operationId="findPetById",
     *     @OAS\Parameter(
     *         description="ID of pet to fetch",
     *         format="int64",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     produces={
     *         "application/json",
     *         "application/xml",
     *         "text/html",
     *         "text/xml"
     *     },
     *     @OAS\Response(
     *         response=200,
     *         description="pet response",
     *         @OAS\Schema(ref="#/definitions/Pet")
     *     ),
     *     @OAS\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OAS\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function findPetById()
    {
    }

    /**
     * @OAS\Get(
     *     path="/pets",
     *     description="Returns all pets from the system that the user has access to",
     *     operationId="findPets",
     *     produces={"application/json", "application/xml", "text/xml", "text/html"},
     *     @OAS\Parameter(
     *         name="tags",
     *         in="query",
     *         description="tags to filter by",
     *         required=false,
     *         type="array",
     *         @OAS\Items(type="string"),
     *         collectionFormat="csv"
     *     ),
     *     @OAS\Parameter(
     *         name="limit",
     *         in="query",
     *         description="maximum number of results to return",
     *         required=false,
     *         type="integer",
     *         format="int32"
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="pet response",
     *         @OAS\Schema(
     *             type="array",
     *             @OAS\Items(ref="#/definitions/Pet")
     *         ),
     *     ),
     *     @OAS\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OAS\Schema(
     *             ref="#/definitions/ErrorModel"
     *         )
     *     ),
     *     @OAS\ExternalDocumentation(
     *         description="find more info here",
     *         url="https://swagger.io/about"
     *     )
     * )
     */
    public function findPets()
    {
    }

    /**
     * @OAS\Delete(
     *     path="/pets/{id}",
     *     description="deletes a single pet based on the ID supplied",
     *     operationId="deletePet",
     *     @OAS\Parameter(
     *         description="ID of pet to delete",
     *         format="int64",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @OAS\Response(
     *         response=204,
     *         description="pet deleted"
     *     ),
     *     @OAS\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OAS\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function deletePet()
    {
    }
}
