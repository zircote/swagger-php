<?php
namespace Petstore\Models;

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
use Swagger\Annotations\Property;
use Swagger\Annotations\Model;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Model(id="Order")
 */
class Order
{
    /**
     * @var int
     * @Property(name="id",type="integer",format="int64")
     */
    protected $id;

    /**
     * @var int
     * @Property(name="petId",type="integer",format="int64")
     */
    protected $petId;

    /**
     * @var string
     * @Property(name="status",type="string",
     *      enum="['placed','approved','delivered']",
     *      description="Order Status"
     *  )
     */
    protected $status;

    /**
     * @var int
     * @Property(name="quantity",type="integer")
     */
    protected $quantity;

    /**
     * @var string
     * @Property(name="shipDate",type="string",format="date")
     */
    protected $shipDate;
}

