<?php
namespace OpenApi\LinkExample;

/**
 * @OAS\Schema(schema="user", type="object")
 */
class User
{

    /**
     * @OAS\Property()
     * @var string
     */
    public $username;

    /**
     * @OAS\Property()
     * @var string
     */
    public $uuid;
}
