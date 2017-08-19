<?php
namespace OpenApi\LinkExample;

/**
 * @SWG\Schema(schema="repository", type="object")
 */
class Repository
{

    /**
     * @SWG\Property()
     * @var string
     */
    public $slug;

    /**
     * @SWG\Property()
     * @var User
     */
    public $owner;
}
