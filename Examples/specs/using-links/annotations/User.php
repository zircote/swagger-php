<?php

namespace OpenApi\Examples\Specs\UsingLinks\Annotations;

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
