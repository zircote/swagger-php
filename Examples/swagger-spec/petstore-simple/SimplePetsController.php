<?php

namespace Petstore;

class SimplePetsController
{

    /**
     * @OAS\Get(
     *     path="/pets",
     *     description="Returns all pets from the system that the user has access to",
     *     operationId="findPets",
     *     @OAS\Parameter(
     *         name="tags",
     *         in="query",
     *         description="tags to filter by",
     *         required=false,
     *         @OAS\Schema(
     *             type="array",
     *             @OAS\Items(type="string"),
     *         ),
     *         style="form"
     *     ),
     *     @OAS\Parameter(
     *         name="limit",
     *         in="query",
     *         description="maximum number of results to return",
     *         required=false,
     *         @OAS\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="pet response",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                 type="array",
     *                 @OAS\Items(ref="#/components/schemas/Pet")
     *             ),
     *         ),
     *         @OAS\MediaType(
     *             mediaType="application/xml",
     *             @OAS\Schema(
     *                 type="array",
     *                 @OAS\Items(ref="#/components/schemas/Pet")
     *             ),
     *         ),
     *         @OAS\MediaType(
     *             mediaType="text/xml",
     *             @OAS\Schema(
     *                 type="array",
     *                 @OAS\Items(ref="#/components/schemas/Pet")
     *             ),
     *         ),
     *         @OAS\MediaType(
     *             mediaType="text/html",
     *             @OAS\Schema(
     *                 type="array",
     *                 @OAS\Items(ref="#/components/schemas/Pet")
     *             ),
     *         ),
     *     ),
     *     @OAS\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/ErrorModel"
     *             )
     *         ),
     *         @OAS\MediaType(
     *             mediaType="application/xml",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/ErrorModel"
     *             )
     *         ),
     *         @OAS\MediaType(
     *             mediaType="text/xml",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/ErrorModel"
     *             )
     *         ),
     *         @OAS\MediaType(
     *             mediaType="text/html",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/ErrorModel"
     *             )
     *         )
     *     )
     * )
     */
    public function findPets()
    {
    }

    /**
     * @OAS\Get(
     *     path="/pets/{id}",
     *     description="Returns a user based on a single ID, if the user does not have access to the pet",
     *     operationId="findPetById",
     *     @OAS\Parameter(
     *         description="ID of pet to fetch",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OAS\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="pet response",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(ref="#/components/schemas/Pet")
     *         ),
     *         @OAS\MediaType(
     *             mediaType="application/xml",
     *             @OAS\Schema(ref="#/components/schemas/Pet")
     *         ),
     *         @OAS\MediaType(
     *             mediaType="text/xml",
     *             @OAS\Schema(ref="#/components/schemas/Pet")
     *         ),
     *         @OAS\MediaType(
     *             mediaType="text/html",
     *             @OAS\Schema(ref="#/components/schemas/Pet")
     *         ),
     *     ),
     *     @OAS\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(ref="#/components/schemas/ErrorModel")
     *         ),
     *         @OAS\MediaType(
     *             mediaType="application/xml",
     *             @OAS\Schema(ref="#/components/schemas/ErrorModel")
     *         ),
     *         @OAS\MediaType(
     *             mediaType="text/xml",
     *             @OAS\Schema(ref="#/components/schemas/ErrorModel")
     *         ),
     *         @OAS\MediaType(
     *             mediaType="text/html",
     *             @OAS\Schema(ref="#/components/schemas/ErrorModel")
     *         ),
     *     )
     * )
     */
    public function findPetById()
    {
    }

    /**
     * @OAS\Post(
     *     path="/pets",
     *     operationId="addPet",
     *     description="Creates a new pet in the store.  Duplicates are allowed",
     *     @OAS\RequestBody(
     *         description="Pet to add to the store",
     *         required=true,
     *         @OAS\MediaType(
     *             mediaType="multipart/form-data",
     *             @OAS\Schema(ref="#/components/schemas/NewPet")
     *         )
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="pet response",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(ref="#/components/schemas/Pet")
     *         )
     *     ),
     *     @OAS\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(ref="#/components/schemas/ErrorModel")
     *         )
     *     )
     * )
     */
    public function addPet()
    {
    }

    /**
     * @OAS\Delete(
     *     path="/pets/{id}",
     *     description="deletes a single pet based on the ID supplied",
     *     operationId="deletePet",
     *     @OAS\Parameter(
     *         description="ID of pet to delete",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OAS\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OAS\Response(
     *         response=204,
     *         description="pet deleted"
     *     ),
     *     @OAS\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OAS\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function deletePet()
    {
    }
}
