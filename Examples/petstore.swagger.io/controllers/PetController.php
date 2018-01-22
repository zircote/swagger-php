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
     *         style="form"
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\Schema(
     *             type="array",
     *             @OAS\Items(ref="#/components/schemas/Pet")
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
     *         style="form"
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                type="array",
     *                @OAS\Items(ref="#/components/schemas/Pet")
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
     *            @OAS\Schema(ref="#/components/schemas/Pet")
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
     *     @OAS\RequestBody(
     *         description="Pet object that needs to be added to the store",
     *         required=true,
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(ref="#/components/schemas/Pet")
     *         ),
     *         @OAS\MediaType(
     *             mediaType="application/xml",
     *             @OAS\Schema(ref="#/components/schemas/Pet")
     *         ),
     *     ),
     *     @OAS\RequestBody(
     *         description="Pet object that needs to be added to the store",
     *         required=true,
     *         @OAS\MediaType(
     *             mediaType="application/xml",
     *             @OAS\Schema(ref="#/components/schemas/Pet")
     *         )
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
     *         required=true,
     *         description="Pet object that needs to be added to the store",
     *         @OAS\MediaType(
     *            mediaType="application/json",
     *            @OAS\Schema(ref="#/components/schemas/Pet"),
     *         ),
     *         @OAS\MediaType(
     *            mediaType="application/xml",
     *            @OAS\Schema(ref="#/components/schemas/Pet"),
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
     *         description="Api key header",
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
     *   @OAS\RequestBody(
     *       required=false,
     *       @OAS\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OAS\Schema(
     *               type="object",
     *               @OAS\Property(
     *                   property="name",
     *                   description="Updated name of the pet",
     *                   type="string"
     *               ),
     *               @OAS\Property(
     *                   property="status",
     *                   description="Updated status of the pet",
     *                   type="string"
     *               ),
     *           )
     *       )
     *   ),
     *   @OAS\Parameter(
     *     name="petId",
     *     in="path",
     *     description="ID of pet that needs to be updated",
     *     required=true,
     *     @OAS\Schema(
     *         type="integer",
     *         format="int64"
     *     )
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
     *     description="",
     *     summary="uploads an image",
     *     operationId="uploadFile",
     *     @OAS\RequestBody(
     *         required=true,
     *         @OAS\MediaType(
     *             mediaType="multipart/form-data",
     *             @OAS\Schema(
     *                 type="object",
     *                 @OAS\Property(
     *                     description="Additional data to pass to server",
     *                     property="additionalMetadata",
     *                     type="string"
     *                 ),
     *                 @OAS\Property(
     *                     description="file to upload",
     *                     property="file",
     *                     type="string",
     *                     format="file",
     *                 ),
     *                 required={"file"}
     *             )
     *         )
     *     ),
     *     @OAS\Parameter(
     *         description="ID of pet to update",
     *         in="path",
     *         name="petId",
     *         required=true,
     *         @OAS\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *     ),
     *     @OAS\Response(
     *         response="200",
     *         description="successful operation",
     *         @OAS\Schema(ref="#/components/schemas/ApiResponse")
     *     ),
     *     security={
     *         {
     *             "petstore_auth": {
     *                  "read:pets",
     *                  "write:pets"
     *             }
     *         }
     *     },
     *     tags={
     *         "pet"
     *     }
     * )
     * */
    public function uploadFile()
    {
    }
}
