<?php
namespace OpenApi\LinkExample;

/**
 * @OAS\Schema(schema="user", type="object",
 *  @OAS\Xml(name="User"))
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
