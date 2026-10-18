<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Spec\Models;

use OpenApi\Spec as OA;

/**
 * Class User.
 */
#[OA\Schema(title: 'User model', description: 'User model')]
class User
{
    #[OA\Property]
    #[OA\Schema(title: 'ID', description: 'ID', format: OA\Format::Int64)]
    private int $id;

    #[OA\Property]
    #[OA\Schema(title: 'Username', description: 'Username')]
    private string $username;

    #[OA\Schema(title: 'First name', description: 'First name')]
    private string $firstName;

    #[OA\Schema(title: 'Last name', description: 'Last name')]
    private string $lastName;

    #[OA\Schema(title: 'Email', description: 'Email', format: OA\Format::Email)]
    private string $email;

    #[OA\Property]
    #[OA\Schema(title: 'Password', description: 'Password', maximum: 255)]
    private string $password;

    #[OA\Property]
    #[OA\Schema(title: 'Phone', description: 'Phone', format: 'msisdn')]
    private string $phone;

    #[OA\Property]
    #[OA\Schema(title: 'User status', description: 'User status', format: OA\Format::Int32)]
    private int $userStatus;
}
