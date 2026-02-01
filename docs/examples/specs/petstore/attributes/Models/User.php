<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes\Models;

use OpenApi\Attributes as OAT;

/**
 * Class User.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
#[OAT\Schema(
    title: 'User model',
    description: 'User model'
)]
class User
{
    #[OAT\Property(
        title: 'ID',
        description: 'ID',
        format: 'int64'
    )]
    private readonly int $id;

    #[OAT\Property(
        title: 'Username',
        description: 'Username'
    )]
    private readonly string $username;

    #[OAT\Property(
        title: 'First name',
        description: 'First name'
    )]
    private readonly string $firstName;

    #[OAT\Property(
        title: 'Last name',
        description: 'Last name'
    )]
    private readonly string $lastName;

    #[OAT\Property(
        title: 'Email',
        description: 'Email',
        format: 'email'
    )]
    private readonly string $email;

    #[OAT\Property(
        title: 'Password',
        description: 'Password',
        maximum: 255
    )]
    private readonly string $password;

    #[OAT\Property(
        title: 'Phone',
        description: 'Phone',
        format: 'msisdn'
    )]
    private readonly string $phone;

    #[OAT\Property(
        title: 'User status',
        description: 'User status',
        format: 'int32'
    )]
    private readonly int $userStatus;
}
