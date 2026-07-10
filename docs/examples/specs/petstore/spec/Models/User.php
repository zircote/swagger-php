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
    #[OA\Property(property: 'id')]
    #[OA\Schema(title: 'ID', description: 'ID', format: 'int64')]
    private int $id;

    #[OA\Property(property: 'username')]
    #[OA\Schema(title: 'Username', description: 'Username')]
    private string $username;

    #[OA\Property(property: 'firstName')]
    #[OA\Schema(title: 'First name', description: 'First name')]
    private string $firstName;

    #[OA\Property(property: 'lastName')]
    #[OA\Schema(title: 'Last name', description: 'Last name')]
    private string $lastName;

    #[OA\Property(property: 'email')]
    #[OA\Schema(title: 'Email', description: 'Email', format: 'email')]
    private string $email;

    #[OA\Property(property: 'password')]
    #[OA\Schema(title: 'Password', description: 'Password', maximum: 255)]
    private string $password;

    #[OA\Property(property: 'phone')]
    #[OA\Schema(title: 'Phone', description: 'Phone', format: 'msisdn')]
    private string $phone;

    #[OA\Property(property: 'userStatus')]
    #[OA\Schema(title: 'User status', description: 'User status', format: 'int32')]
    private int $userStatus;
}
