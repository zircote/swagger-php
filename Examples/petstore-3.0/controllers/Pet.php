<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace Petstore30\controllers;


/**
 * Class Pet
 *
 * @package Petstore30\controllers
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class Pet
{
    /**
     * @OAS\Post(
     *     path="/pet",
     *     tags={"pet"},
     *     summary="Add a new pet to the store",
     *     operationId="addPet",
     *     @OAS\Response(
     *         response=405,
     *         description="Invalid input"
     *     ),
     *     security={
     *         {"petstore_auth": {"write:pets", "read:pets"}}
     *     },
     *     requestBody={"$ref": "#/components/requestBodies/Pet"}
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function addPet()
    {
    }

    /**
     * @OAS\Put(
     *     path="/pet",
     *     tags={"pet"},
     *     summary="Update an existing pet",
     *     operationId="updatePet",
     *     @OAS\Response(
     *         response=400,
     *         description="Invalid ID supplied"
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="Pet not found"
     *     ),
     *     @OAS\Response(
     *         response=405,
     *         description="Validation exception"
     *     ),
     *     security={
     *         {"petstore_auth": {"write:pets", "read:pets"}}
     *     },
     *     requestBody={"$ref": "#/components/requestBodies/Pet"}
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function updatePet()
    {
    }

    /**
     * @OAS\Get(
     *     path="/pet/findByStatus",
     *     tags={"pet"},
     *     summary="Finds Pets by status",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="findPetsByStatus",
     *     deprecated=true,
     *     @OAS\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status values that needed to be considered for filter",
     *         required=true,
     *         explode=true,
     *         @OAS\Schema(
     *             type="array",
     *             default="available",
     *             @OAS\Items(
     *                 type="string",
     *                 enum = {"available", "pending", "sold"},
     *             )
     *         )
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                 type="array",
     *                 @OAS\Items(
     *                    ref="#/components/schemas/Pet"
     *                 )
     *             )
     *         ),
     *         @OAS\MediaType(
     *             mediaType="application/xml",
     *             @OAS\Schema(
     *                 type="array",
     *                 @OAS\Items(
     *                    ref="#/components/schemas/Pet"
     *                 )
     *             )
     *         )
     *     ),
     *     @OAS\Response(
     *         response=400,
     *         description="Invalid status value"
     *     ),
     *     security={
     *         {"petstore_auth": {"write:pets", "read:pets"}}
     *     }
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function findPetsByStatus()
    {
    }

    /**
     * @OAS\Get(
     *     path="/pet/findByTags",
     *     tags={"pet"},
     *     summary="Finds Pets by tags",
     *     description=">-
    Muliple tags can be provided with comma separated strings. Use\ \ tag1,
    tag2, tag3 for testing.",
     *     operationId="findByTags",
     *     @OAS\Parameter(
     *         name="tags",
     *         in="query",
     *         description="Tags to filter by",
     *         required=true,
     *         explode=true,
     *         @OAS\Schema(
     *             type="array",
     *             @OAS\Items(
     *                 type="string",
     *             )
     *         )
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                 type="array",
     *                 @OAS\Items(
     *                    ref="#/components/schemas/Pet"
     *                 )
     *             )
     *         ),
     *         @OAS\MediaType(
     *             mediaType="application/xml",
     *             @OAS\Schema(
     *                 type="array",
     *                 @OAS\Items(
     *                    ref="#/components/schemas/Pet"
     *                 )
     *             )
     *         )
     *     ),
     *     @OAS\Response(
     *         response=400,
     *         description="Invalid status value"
     *     ),
     *     security={
     *         {"petstore_auth": {"write:pets", "read:pets"}}
     *     }
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function findByTags()
    {
    }

    /**
     * @OAS\Get(
     *     path="/pet/{petId}",
     *     tags={"pet"},
     *     summary="Find pet by ID",
     *     description="Returns a single pet",
     *     operationId="getPetById",
     *     @OAS\Parameter(
     *         name="petId",
     *         in="path",
     *         description="ID of pet to return",
     *         required=true,
     *         @OAS\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/Pet"
     *             ),
     *         ),
     *         @OAS\MediaType(
     *             mediaType="application/xml",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/Pet"
     *             ),
     *         )
     *     ),
     *     @OAS\Response(
     *         response=400,
     *         description="Invalid ID supplier"
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="Pet not found"
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     *
     * @param int $id
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function getPetById($id)
    {
    }

    /**
     * @OAS\Post(
     *     path="/pet/{petId}",
     *     tags={"pet"},
     *     summary="Updates a pet in the store with form data",
     *     operationId="updatePetWithForm",
     *     @OAS\Parameter(
     *         name="petId",
     *         in="path",
     *         description="ID of pet that needs to be updated",
     *         required=true,
     *         @OAS\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OAS\Response(
     *         response=405,
     *         description="Invalid input"
     *     ),
     *     security={
     *         {"petstore_auth": {"write:pets", "read:pets"}}
     *     },
     *     @OAS\RequestBody(
     *         description="Input data format",
     *         @OAS\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OAS\Schema(
     *                 type="object",
     *                 @OAS\Property(
     *                     property="name",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                 @OAS\Property(
     *                     property="status",
     *                     description="Updated status of the pet",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function updatePetWithForm()
    {
    }

    /**
     * @OAS\Delete(
     *     path="/pet/{petId}",
     *     tags={"pet"},
     *     summary="Deletes a pet",
     *     operationId="deletePet",
     *     @OAS\Parameter(
     *         name="api_key",
     *         in="header",
     *         required=false,
     *         @OAS\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OAS\Parameter(
     *         name="petId",
     *         in="path",
     *         description="Pet id to delete",
     *         required=true,
     *         @OAS\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *     ),
     *     @OAS\Response(
     *         response=400,
     *         description="Invalid ID supplied",
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="Pet not found",
     *     ),
     *     security={
     *         {"petstore_auth": {"write:pets", "read:pets"}}
     *     },
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function deletePet()
    {
    }

    /**
     * @OAS\Post(
     *     path="/pet/{petId}/uploadImage",
     *     tags={"pet"},
     *     summary="uploads an image",
     *     operationId="uploadFile",
     *     @OAS\Parameter(
     *         name="petId",
     *         in="path",
     *         description="ID of pet to update",
     *         required=true,
     *         @OAS\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1
     *         )
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/ApiResponse"
     *             )
     *         )
     *     ),
     *     security={
     *         {"petstore_auth": {"write:pets", "read:pets"}}
     *     },
     *     @OAS\RequestBody(
     *         description="Upload images request body",
     *         @OAS\MediaType(
     *             mediaType="application/octet-stream",
     *             @OAS\Schema(
     *                 type="string",
     *                 format="binary"
     *             )
     *         )
     *     )
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function uploadFile()
    {
    }
}