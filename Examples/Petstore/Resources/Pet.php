<?php
namespace Petstore\Resources;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2014] [Robert Allen]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package
 * @category
 * @subpackage
 */
use Swagger\Annotations as SWG;

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   basePath="http://petstore.swagger.wordnik.com/api",
 *   resourcePath="/pet",
 *   description="Operations about pets",
 *   produces="['application/json','application/xml','text/plain','text/html']"
 * )
 */
class Pet
{
    /**
     * @SWG\Api(
     *   path="/pet/{petId}",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="Find pet by ID",
     *     notes="Returns a pet based on ID",
     *     type="Pet",
     *     authorizations={},
     *     @SWG\Parameter(
     *       name="petId",
     *       description="ID of pet that needs to be fetched",
     *       required=true,
     *       type="integer",
     *       format="int64",
     *       paramType="path",
     *       minimum="1.0",
     *       maximum="100000.0",
     *       allowMultiple=false
     *     ),
     *     @SWG\ResponseMessage(code=400, message="Invalid ID supplied"),
     *     @SWG\ResponseMessage(code=404, message="Pet not found")
     *   )
     * )
     */
    public function getPetById() {

    }

    /**
     * @SWG\Api(
     *   path="/pet/{petId}",
     *   @SWG\Operation(
     *     method="DELETE",
     *     summary="Deletes a pet",
     *     notes="",
     *     type="void",
     *     @SWG\Parameter(
     *       name="petId",
     *       description="Pet id to delete",
     *       required=true,
     *       type="string",
     *       paramType="path",
     *       allowMultiple=false
     *     ),
     *     authorizations="oauth2.write:pets",
     *     @SWG\ResponseMessage(code=400, message="Invalid pet value")
     *   )
     * )
     */
    public function deletePet() {

    }

    /**
     * @SWG\Api(
     *   path="/pet/{petId}",
     *   @SWG\Operation(
     *     method="PATCH",
     *     summary="partial updates to a pet",
     *     notes="",
     *     type="array",
     *     @SWG\Items("Pet"),
     *     nickname="partialUpdate",
     *     @SWG\Produces("application/json"),
     *     @SWG\Produces("application/xml"),
     *     @SWG\Consumes("application/json"),
     *     @SWG\Consumes("application/xml"),
     *     authorizations="oauth2.write:pets",
     *     @SWG\Parameter(
     *       name="petId",
     *       description="ID of pet that needs to be fetched",
     *       required=true,
     *       type="string",
     *       paramType="path",
     *       allowMultiple=false
     *     ),
     *     @SWG\Parameter(
     *       name="body",
     *       description="Pet object that needs to be added to the store",
     *       required=true,
     *       type="Pet",
     *       paramType="body",
     *       allowMultiple=false
     *     ),
     *     @SWG\ResponseMessage(code=400, message="Invalid tag value")
     *   )
     * )
     */
    function partialUpdate() {

    }


    /**
     * @SWG\Api(
     *   path="/pet/{petId}",
     *   @SWG\Operation(
     *     method="POST",
     *     summary="Updates a pet in the store with form data",
     *     notes="",
     *     type="void",
     *     @SWG\Consumes("application/x-www-form-urlencoded"),
     *     authorizations="oauth2.write:pets",
     *     @SWG\Parameter(
     *       name="petId",
     *       description="ID of pet that needs to be updated",
     *       required=true,
     *       type="string",
     *       paramType="path",
     *       allowMultiple=false
     *     ),
     *     @SWG\Parameter(
     *       name="name",
     *       description="Updated name of the pet",
     *       required=false,
     *       type="string",
     *       paramType="form",
     *       allowMultiple=false
     *     ),
     *     @SWG\Parameter(
     *       name="status",
     *       description="Updated status of the pet",
     *       required=false,
     *       type="string",
     *       paramType="form",
     *       allowMultiple=false
     *     ),
     *     @SWG\ResponseMessage(code=405, message="Invalid input")
     *   )
     * )
     */
    function updatePetWithForm() {

    }
    

    /**
     * @SWG\Api(
     *   path="/pet",
     *   @SWG\Operation(
     *     method="POST",
     *     summary="Add a new pet to the store",
     *     notes="",
     *     type="void",
     *     @SWG\Consumes("application/json"),
     *     @SWG\Consumes("application/xml"),
     *     authorizations="oauth2.write:pets",
     *     @SWG\Parameter(
     *       name="body",
     *       description="Pet object that needs to be added to the store",
     *       required=true,
     *       type="Pet",
     *       paramType="body",
     *       allowMultiple=false
     *     ),
     *     @SWG\ResponseMessage(code=405, message="Invalid input")
     *   )
     * )
     */
    function addPet() {

    }

    /**
     * @SWG\Api(
     *   path="/pet",
     *   @SWG\Operation(
     *     method="PUT",
     *     summary="Update an existing pet",
     *     notes="",
     *     type="void",
     *     authorizations={},
     *     @SWG\Parameter(
     *       name="body",
     *       description="Pet object that needs to be updated in the store",
     *       required=true,
     *       type="Pet",
     *       paramType="body",
     *       allowMultiple=false
     *     ),
     *     @SWG\ResponseMessage(code=400, message="Invalid ID supplied"),
     *     @SWG\ResponseMessage(code=404, message="Pet not found"),
     *     @SWG\ResponseMessage(code=405, message="Validation exception")
     *   )
     * )
     */
    function updatePet() {

    }

    /**
     * @SWG\Api(
     *   path="/pet/findByStatus",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="Finds Pets by status",
     *     notes="Multiple status values can be provided with comma seperated strings",
     *     type="array",
     *     items="$ref:Pet",
     *     nickname="findPetsByStatus",
     *     authorizations={},
     *     @SWG\Parameter(
     *       name="status",
     *       description="Status values that need to be considered for filter",
     *       defaultValue="available",
     *       required=true,
     *       type="string",
     *       paramType="query",
     *       allowMultiple=true,
     *       enum="['available','pending','sold']"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="Invalid status value")
     *   )
     * )
     */
    function findByStatus() {

    }
        

    /**
     * @SWG\Api(path="/pet/findByTags",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="Finds Pets by tags",
     *     notes="Muliple tags can be provided with comma seperated strings. Use tag1, tag2, tag3 for testing.",
     *     type="array",
     *     @SWG\Items("Pet"),
     *     nickname="findPetsByTags",
     *     authorizations={},
     *     @SWG\Parameter(
     *       name="tags",
     *       description="Tags to filter by",
     *       required=true,
     *       type="string",
     *       paramType="query",
     *       allowMultiple=true
     *     ),
     *     @SWG\ResponseMessage(code=400, message="Invalid tag value"),
     *     deprecated="true"
     *   )
     * )
     */
    function findPetsByTags();

    /**
     * @SWG\Api(
     *   path="/pet/uploadImage",
     *   @SWG\Operation(
     *     method="POST",
     *     summary="uploads an image",
     *     notes="",
     *     type="void",
     *     nickname="uploadFile",
     *     @SWG\Consumes("multipart/form-data"),
     *     authorizations="oauth2.write:pets, oauth2.read:pets",
     *     @SWG\Parameter(
     *       name="additionalMetadata",
     *       description="Additional data to pass to server",
     *       required=false,
     *       type="string",
     *       paramType="form",
     *       allowMultiple=false
     *     ),
     *     @SWG\Parameter(
     *       name="file",
     *       description="file to upload",
     *       required=false,
     *       type="File",
     *       paramType="form",
     *       allowMultiple=false
     *     )
     *   )
     * )
     */
    function uploadFile() {

    }
}