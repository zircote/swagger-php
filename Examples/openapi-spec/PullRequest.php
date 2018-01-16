<?php
namespace OpenApi\LinkExample;

/**
 * @OAS\Schema(schema="pullrequest", type="object")
 */
class Repository
{

    /**
     * @OAS\Property()
     * @var integer
     */
    public $id;

    /**
     * @OAS\Property()
     * @var string
     */
    public $title;

    /**
    * @OAS\Property()
    * @var Repository
    */
    public $repository;

    /**
     * @OAS\Property()
     * @var User
     */
    public $author;
}
