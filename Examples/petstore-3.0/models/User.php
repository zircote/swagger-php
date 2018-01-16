<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace Petstore30;


/**
 * Class User
 *
 * @package Petstore30
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @OAS\Schema(
 *     title="User model",
 *     description="User model",
 *     type="object"
 * )
 */
class User
{
    /**
     * @OAS\Property(
     *     format="int64",
     *     description="ID",
     *     title="ID",
     * )
     *
     * @var integer
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private $id;

    /**
     * @OAS\Property(
     *     description="Username",
     *     title="Username",
     * )
     *
     * @var string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private $username;

    /**
     * @OAS\Property(
     *     description="First name",
     *     title="First name",
     * )
     *
     * @var string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private $firstName;

    /**
     * @OAS\Property(
     *     description="Last name",
     *     title="Last name",
     * )
     *
     * @var string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private $lastName;

    /**
     * @OAS\Property(
     *     format="email",
     *     description="Email",
     *     title="Email",
     * )
     *
     * @var string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private $email;

    /**
     * @OAS\Property(
     *     format="int64",
     *     description="Password",
     *     title="Password",
     *     maximum=255
     * )
     *
     * @var string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private $password;

    /**
     * @OAS\Property(
     *     format="msisdn",
     *     description="Phone",
     *     title="Phone",
     * )
     *
     * @var string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private $phone;

    /**
     * @OAS\Property(
     *     format="int32",
     *     description="User status",
     *     title="User status",
     * )
     *
     * @var integer
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private $userStatus;
}