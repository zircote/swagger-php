<?php

namespace OpenApi\Examples\UsingLinks;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="user")
 */
class User
{

    /**
     * @OA\Property
     *
     * @var string
     */
    public $username;

    /**
     * @OA\Property
     *
     * @var string
     */
    public $uuid;
}
