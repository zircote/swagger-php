<?php
namespace OpenApi\LinkExample;

/**
 * @OA\Schema(schema="user", type="object")
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
