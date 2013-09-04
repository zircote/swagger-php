<?php
namespace SwaggerTests\Fixtures\Models;

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
use Swagger\Annotations\AllowableValues;
use Swagger\Annotations\Model;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Model(id="User")
 */
class User
{
    /**
     * @var int
     * @Property(name="id",type="long")
     */
    protected $id;

    /**
     * @var string
     * @Property(name="lastName",type="string")
     */
    protected $lastName;

    /**
     * @var string
     * @Property(name="phone",type="string")
     */
    protected $phone;

    /**
     * @var string
     * @Property(name="username",type="string")
     */
    protected $username;

    /**
     * @var string
     * @Property(name="email",type="string")
     */
    protected $email;

    /**
     * @var int
     * @Property(name="userStatus",type="int",
     *      @allowableValues(
     *          valueType="LIST",
     *          values="{'1': 'registered', '2': 'active', '3': 'closed'}"
     *      ),
     *      description="User Status"
     * )
     */
    protected $userStatus;

    /**
     * @var string
     * @Property(name="firstName",type="string")
     */
    protected $firstName;

    /**
     * @var string
     * @Property(name="password",type="string")
     */
    protected $password;
}

