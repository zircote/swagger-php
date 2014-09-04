<?php

namespace Petstore\Models;

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
 * @SWG\Model(id="User")
 */
class User
{
    /**
     * @SWG\Property(
     *   name="id",
     *   type="integer",
     *   format="int64"
     * )
     */
    public $id;

    /**
     * @SWG\Property(name="firstName",type="string")
     */
    public $firstname;

    /**
     * @SWG\Property(name="username",type="string")
     */
    public $username;

    /**
     * @SWG\Property(name="lastName",type="string")
     */
    public $lastname;

    /**
     * @SWG\Property(name="email",type="string")
     */
    public $email;

    /**
     * @SWG\Property(name="password",type="string")
     */
    public $password;

    /**
     * @SWG\Property(name="phone",type="string")
     */
    public $phone;

    /**
     * @SWG\Property(
     *   name="userStatus", type="integer", format="int32",
     *   description="User Status",
     *   enum="{'1':'registered','2':'active','3':'closed'}"
     * )
     */
    public $status;

}