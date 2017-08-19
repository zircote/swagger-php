<?php
namespace OpenApi\LinkExample;

/**
 * @SWG\Schema(schema="user", type="object")
 */
class User
{

    /**
     * @SWG\Property()
     * @var string
     */
    public $username;

    /**
     * @SWG\Property()
     * @var string
     */
    public $uuid;
}
