<?php

namespace Openapi\Snippets\Cookbook\PropertyConstraints;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="User")
 */
class User
{
    /**
     * @OA\Property(maxLength=50)
     */
    public string $name;

    /**
     * @OA\Property(format="email")
     */
    public string $email;
}
