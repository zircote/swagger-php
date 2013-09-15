<?php
namespace Minimal\Models;

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
 *
 * @SWG\Model()
 * Model() will use the classname "Pet" as id
 */
class Pet
{
    /**
     * @var array<Tags>
     *
     * @SWG\Property(type="array", items="$ref:Tag")
	 * Property() will use the property name "tags" as name.
     */
    protected $tags = array();

    /**
     * @var int
     *
     * @SWG\Property()
	 * Property() will use the property name "id" as name and detect the "@var int" and use "integer" as type.
     */
    protected $id;

    /**
     * @var Category
     *
     * @SWG\Property()
     */
    protected $category;

    /**
     *
     *
     * @var string
     *
     * @SWG\Property(
     *      enum="['available', 'pending', 'sold']",
     *      description="pet status in the store")
     */
    protected $status;

    /**
     * @var string
     *
     * @SWG\Property()
     */
    protected $name;

    /**
     * @var array<string>
     *
     * @SWG\Property(type="array", @SWG\items(type="string"))
     */
    protected $photoUrls = array();
}

