<?php

namespace OpenApi\Examples\OpenapiSpec;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="user")
 */
class User
{

    /**
     * @OA\Property()
     * @var string
     */
    public $username;

    /**
     * @OA\Property()
     * @var string
     */
    public $uuid;
}
