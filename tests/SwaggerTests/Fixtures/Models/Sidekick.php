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

/**
 * @package
 * @category
 * @subpackage
 *
 * @Model(id="Sidekick")
 */
class Sidekick
{
    /**
     * @var int
     * @Property(name="id",type="long")
     */
    protected $id;
    /**
     * @var int
     * @property(name="age", type="int", @allowableValues(valueType="RANGE", min=18,max=60))
     */
    protected $age;
}

