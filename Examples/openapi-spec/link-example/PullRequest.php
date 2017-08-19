<?php
namespace OpenApi\LinkExample;

/**
 * @SWG\Schema(schema="pullrequest", type="object")
 */
class Repository
{

    /**
     * @SWG\Property()
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property()
     * @var string
     */
    public $title;

    /**
    * @SWG\Property()
    * @var Repository
    */
    public $repository;

    /**
     * @SWG\Property()
     * @var User
     */
    public $author;
}
