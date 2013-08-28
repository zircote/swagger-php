<?php
namespace SwaggerTests\Fixtures2\Models;

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
use \Swagger\Annotations as SWG;

/**
 * @package
 * @category
 * @subpackage
 *
 *
 * @SWG\Model()
 * Model() will use the classname "Dog" as id and inherit all swagger properties from Pet
 */
abstract class Dog extends Pet
{
    /**
     * @SWG\Property(required=true)
     * @var string
     */
    public $breed;

    /**
     * @var Dog
     *
     * @SWG\Property()
     */
    protected $parent;
}