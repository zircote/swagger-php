<?php

namespace PetstoreIO;

final class PetController
{

    /**
     * @OAS\Get(
     *     path="/pet/findByTags",
     *     summary="Finds Pets by tags",
     *     tags={"pet"},
     *     description="Muliple tags can be provided with comma separated strings. Use tag1, tag2, tag3 for testing.",
     *     operationId="findPetsByTags",
     *     @OAS\Parameter(
     *         name="tags",
     *         in="query",
     *         description="Tags to filter by",
     *         required=true,
     *         @OAS\Schema(
     *           type="array",
     *           @OAS\Items(type="string"),
     *         ),
     *         form="array"
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\Schema(
     *             type="array",
     *             @OAS\Items(ref="#/definitions/Pet")
     *         ),
     *     ),
     *     @OAS\Response(
     *         response="400",
     *         description="Invalid tag value",
     *     ),
     *     security={
     *         {"petstore_auth": {"write:pets", "read:pets"}}
     *     },
     *     deprecated=true
     * )
     */
    public function findByTags()
    {
    }

    /**
     * @OAS\Get(
     *     path="/pet/findByStatus",
     *     summary="Finds Pets by status",
     *     description="Multiple status values can be provided with comma separated strings",
     *     operationId="findPetsByStatus",
     *     tags={"pet"},
     *     @OAS\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status values that need to be considered for filter",
     *         required=true,
     *         @OAS\Schema(
     *         type="array",
     *           @OAS\Items(
     *               type="string",
     *               enum={"available", "pending", "sold"},
     *               default="available"
     *           ),
     *         ),
     *         form="array"
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                type="array",
     *                @OAS\Items(ref="#/definitions/Pet")
     *             ),
     *         )
     *     ),
     *     @OAS\Response(
     *         response="400",
     *         description="Invalid status value",
     *     ),
     *     security={
     *       {"petstore_auth": {"write:pets", "read:pets"}}
     *     }
     * )
     */
    public function findByStatus()
    {
    }

    /**
     * @OAS\Get(
     *     path="/pet/{petId}",
     *     summary="Find pet by ID",
     *     description="Returns a single pet",
     *     operationId="getPetById",
     *     tags={"pet"},
     *     @OAS\Parameter(
     *         description="ID of pet to return",
     *         in="path",
     *         name="petId",
     *         required=true,
     *         @OAS\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\MediaType(
     *            mediaType="application/json",
     *            @OAS\Schema(ref="#/definitions/Pet")
     *         )
     *     ),
     *     @OAS\Response(
     *         response="400",
     *         description="Invalid ID supplied"
     *     ),
     *     @OAS\Response(
     *         response="404",
     *         description="Pet not found"
     *     ),
     *     security={
     *       {"api_key": {}}
     *     }
     * )
     */
    public function getPetById()
    {
    }

    /**
     * @OAS\Post(
     *     path="/pet",
     *     tags={"pet"},
     *     operationId="addPet",
     *     summary="Add a new pet to the store",
     *     description="",
     *     consumes={"application/json", "application/xml"},
     *     @OAS\RequestBody(
     *         request="body",
     *         description="Pet object that needs to be added to the store",
     *         required=true,
     *         @OAS\Schema(ref="#/components/schemes/Pet"),
     *     ),
     *     @OAS\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"petstore_auth":{"write:pets", "read:pets"}}}
     * )
     */
    public function addPet()
    {
    }

    /**
     * @OAS\Put(
     *     path="/pet",
     *     tags={"pet"},
     *     operationId="updatePet",
     *     summary="Update an existing pet",
     *     description="",
     *     @OAS\RequestBody(
     *         request="body",
     *         description="Pet object that needs to be added to the store",
     *         @OAS\MediaType(
     *            mediaType="application/json"
     *            @OAS\Schema(ref="#/definitions/Pet", required=true),
     *         ),
     *         @OAS\MediaType(
     *            mediaType="application/xml"
     *            @OAS\Schema(ref="#/definitions/Pet", required=true),
     *         )
     *     ),
     *     @OAS\Response(
     *         response=400,
     *         description="Invalid ID supplied",
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="Pet not found",
     *     ),
     *     @OAS\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     *     security={{"petstore_auth":{"write:pets", "read:pets"}}}
     * )
     */
    public function updatePet()
    {
    }

    /**
     * @OAS\Delete(
     *     path="/pet/{petId}",
     *     summary="Deletes a pet",
     *     description="",
     *     operationId="deletePet",
     *     tags={"pet"},
     *     @OAS\Parameter(
     *         description="Pet id to delete",
     *         in="path",
     *         name="petId",
     *         required=true,
     *         @OAS\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OAS\Header(
     *         header="api_key",
     *         required=false,
     *         @OAS\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OAS\Response(
     *         response=400,
     *         description="Invalid ID supplied"
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="Pet not found"
     *     ),
     *     security={{"petstore_auth":{"write:pets", "read:pets"}}}
     * )
     */
    public function deletePet()
    {
    }

    /**
     * @OAS\Post(
     *   path="/pet/{petId}",
     *   tags={"pet"},
     *   summary="Updates a pet in the store with form data",
     *   description="",
     *   operationId="updatePetWithForm",
     *   @OAS\
     *   consumes={"application/x-www-form-urlencoded"},
     *   @OAS\Parameter(
     *     name="petId",
     *     in="path",
     *     description="ID of pet that needs to be updated",
     *     required=true,
     *     type="integer",
     *     format="int64"
     *   ),
     *   @OAS\Parameter(
     *     name="name",
     *     in="formData",
     *     description="Updated name of the pet",
     *     required=false,
     *     type="string"
     *   ),
     *   @OAS\Parameter(
     *     name="status",
     *     in="formData",
     *     description="Updated status of the pet",
     *     required=false,
     *     type="string"
     *   ),
     *   @OAS\Response(response="405",description="Invalid input"),
     *   security={{
     *     "petstore_auth": {"write:pets", "read:pets"}
     *   }}
     * )
     */
    public function updatePetWithForm()
    {
    }

    /**
     * @OAS\Post(
     *     path="/pet/{petId}/uploadImage",
     *     consumes={"multipart/form-data"},
     *     description="",
     *     operationId="uploadFile",
     *     @OAS\Parameter(
     *         description="Additional data to pass to server",
     *         in="formData",
     *         name="additionalMetadata",
     *         required=false,
     *         type="string"
     *     ),
     *     @OAS\Parameter(
     *         description="file to upload",
     *         in="formData",
     *         name="file",
     *         required=false,
     *         type="file"
     *     ),
     *     @OAS\Parameter(
     *         description="ID of pet to update",
     *         format="int64",
     *         in="path",
     *         name="petId",
     *         required=true,
     *         type="integer"
     *     ),
     *     @OAS\Response(
     *         response="200",
     *         description="successful operation",
     *         @OAS\Schema(ref="#/definitions/ApiResponse")
     *     ),
     *     security={
     *         {
     *             "petstore_auth": {
     *                  "read:pets",
     *                  "write:pets"
     *             }
     *         }
     *     },
     *     summary="uploads an image",
     *     tags={
     *         "pet"
     *     }
     * )
     * */
    public function uploadFile()
    {
    }
}
