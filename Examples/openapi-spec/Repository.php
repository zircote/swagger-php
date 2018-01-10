<?php
namespace OpenApi\LinkExample;

/**
 * @OAS\Schema(schema="repository", type="object")
 */
class Repository
{

    /**
     * @OAS\Property()
     * @var string
     */
    public $slug;

    /**
     * @OAS\Property()
     * @var User
     */
    public $owner;
}
