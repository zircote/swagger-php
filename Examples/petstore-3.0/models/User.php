<?php

/**
 * @license Apache 2.0
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
     */
    private $id;

    /**
     * @OAS\Property(
     *     description="Username",
     *     title="Username",
     * )
     *
     * @var string
     */
    private $username;

    /**
     * @OAS\Property(
     *     description="First name",
     *     title="First name",
     * )
     *
     * @var string
     */
    private $firstName;

    /**
     * @OAS\Property(
     *     description="Last name",
     *     title="Last name",
     * )
     *
     * @var string
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
     */
    private $userStatus;
}
