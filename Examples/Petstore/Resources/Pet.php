<?php
namespace Petstore\Resources;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2013] [Robert Allen]
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
 *   basePath="http://localhost:8002/api",
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
     *   deprecated=true,
     *   @SWG\Operation(
     *     method="GET",
     *     deprecated=true,
     *     summary="Find pet by ID",
     *     notes="Returns a pet based on ID",
     *     type="Pet",
     *     nickname="getPetById",
     *     @SWG\Parameter(
     *       name="petId",
     *       description="ID of pet that needs to be fetched",
     *       required=true,
     *       type="integer",
     *       format="int64",
     *       paramType="path",
     *       minimum="1.0",
     *       maximum="100000.0"
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
     *   path="/pet/findByStatus",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="Finds Pets by status",
     *     notes="Multiple status values can be provided with comma seperated strings",
     *     type="array",
     *     items="$ref:Pet",
     *     nickname="findPetsByStatus",
     *     @SWG\Parameter(
     *       name="status",
     *       description="Status values that need to be considered for filter",
     *       defaultValue="available",
     *       required=true,
     *       type="string",
     *       paramType="query",
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
     *     @SWG\Parameter(
     *       name="tags",
     *       description="Tags to filter by",
     *       required=true,
     *       type="string",
     *       paramType="query"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="Invalid tag value"),
     *     deprecated="true"
     *   )
     * )
     */
    function findPetsByTags();
}