<?php

namespace OpenApi\Examples\SwaggerSpec\PetstoreWithExternalDocs\Controllers;

class PetWithDocsController
{
    /**
     * @OA\Post(
     *     path="/pets",
     *     operationId="addPet",
     *     description="Creates a new pet in the store.  Duplicates are allowed",
     *     tags={"store"},
     *     @OA\RequestBody(
     *         description="Pet to add to the store",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/NewPet")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="pet response",
     *         @OA\JsonContent(ref="#/components/schemas/Pet")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function addPet()
    {
    }

    /**
     * @OA\Get(
     *     path="/pets/{id}",
     *     description="Returns a user based on a single ID, if the user does not have access to the pet",
     *     operationId="findPetById",
     *     tags={"store"},
     *     @OA\Parameter(
     *         description="ID of pet to fetch",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="pet response",
     *         @OA\JsonContent(ref="#/components/schemas/Pet"),
     *         @OA\XmlContent(ref="#/components/schemas/Pet"),
     *         @OA\MediaType(
     *             mediaType="text/html",
     *             @OA\Schema(ref="#/components/schemas/Pet")
     *         ),
     *         @OA\MediaType(
     *             mediaType="text/xml",
     *             @OA\Schema(ref="#/components/schemas/Pet")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\XmlContent(ref="#/components/schemas/ErrorModel"),
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *         @OA\MediaType(
     *             mediaType="text/xml",
     *             @OA\Schema(ref="#/components/schemas/ErrorModel")
     *         ),
     *         @OA\MediaType(
     *             mediaType="text/html",
     *             @OA\Schema(ref="#/components/schemas/ErrorModel")
     *         ),
     *     )
     * )
     */
    public function findPetById()
    {
    }

    /**
     * @OA\Get(
     *     path="/pets",
     *     description="Returns all pets from the system that the user has access to",
     *     operationId="findPets",
     *     tags={"store"},
     *     @OA\Parameter(
     *         name="tags",
     *         in="query",
     *         description="tags to filter by",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string"),
     *         ),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="maximum number of results to return",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="pet response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Pet")
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/xml",
     *             @OA\Schema(
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Pet")
     *             ),
     *         ),
     *         @OA\MediaType(
     *             mediaType="text/xml",
     *             @OA\Schema(
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Pet")
     *             ),
     *         ),
     *         @OA\MediaType(
     *             mediaType="text/html",
     *             @OA\Schema(
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Pet")
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\MediaType(
     *             mediaType="application/xml",
     *             @OA\Schema(ref="#/components/schemas/ErrorModel")
     *         ),
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *         @OA\MediaType(
     *             mediaType="text/xml",
     *             @OA\Schema(ref="#/components/schemas/ErrorModel")
     *         ),
     *         @OA\MediaType(
     *             mediaType="text/html",
     *             @OA\Schema(ref="#/components/schemas/ErrorModel")
     *         ),
     *     ),
     *     @OA\ExternalDocumentation(
     *         description="find more info here",
     *         url="https://swagger.io/about"
     *     )
     * )
     */
    public function findPets()
    {
    }

    /**
     * @OA\Delete(
     *     path="/pets/{id}",
     *     description="deletes a single pet based on the ID supplied",
     *     operationId="deletePet",
     *     tags={"store"},
     *     @OA\Parameter(
     *         description="ID of pet to delete",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="pet deleted"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function deletePet()
    {
    }
}
