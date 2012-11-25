<?php
namespace SwaggerTests\Fixtures\Models;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2012] [Robert Allen]
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
use Swagger\Annotations\Property;
use Swagger\Annotations\AllowableValues;
use Swagger\Annotations\Model;
use Swagger\Annotations\Items;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Model(id="Pet")
 */
class Pet
{
    /**
     * @var array<Tags>
     *
     * @Property(name="tags",type="array", items="$ref:Tag")
     */
    protected $tags = array();

    /**
     * @var int
     *
     * @Property(name="id",type="long")
     */
    protected $id;

    /**
     * @var Category
     *
     * @Property(name="category",type="Category")
     */
    protected $category;

    /**
     *
     *
     * @var string
     *
     * @Property(
     *      name="status",type="string",
     *      @allowableValues(
     *          valueType="LIST",
     *          values="['available', 'pending', 'sold']"
     *      ),
     *      description="pet status in the store")
     */
    protected $status;

    /**
     * @var string
     *
     * @Property(name="name",type="string")
     */
    protected $name;

    /**
     * @var array<string>
     *
     * @Property(name="photoUrls",type="array", @items(type="string"))
     */
    protected $photoUrls = array();
}

