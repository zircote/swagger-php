<?php

namespace PetstoreIO;

/**
 * @OAS\Schema(type="object", @OAS\Xml(name="User"))
 */
class User
{

    /**
     * @OAS\Property(format="int64")
     * @var int
     */
    public $id;

    /**
     * @OAS\Property()
     * @var string
     */
    public $username;

    /**
     * @OAS\Property()
     * @var string
     */
    public $firstName;

    /**
     * @OAS\Property()
     * @var string
     */
    public $lastName;

    /**
     * @var string
     * @OAS\Property()
     */
    public $email;

    /**
     * @var string
     * @OAS\Property()
     */
    public $password;

    /**
     * @var string
     * @OAS\Property()
     */
    public $phone;

    /**
     * User Status
     * @var int
     * @OAS\Property(format="int32")
     */
    public $userStatus;
}
