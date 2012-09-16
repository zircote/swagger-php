<?php
namespace SwaggerTests\Fixtures\Models;

/**
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
     * @Property(type="long")
     */
    protected $id;

    /**
     * @var string
     * @Property(type="string")
     */
    protected $lastName;

    /**
     * @var string
     * @Property(type="string")
     */
    protected $phone;

    /**
     * @var string
     * @Property(type="string")
     */
    protected $username;

    /**
     * @var string
     * @Property(type="string")
     */
    protected $email;

    /**
     * @var int
     * @Property(type="integer",
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
     * @Property(type="string")
     */
    protected $firstName;

    /**
     * @var string
     * @Property(type="string")
     */
    protected $password;
}

