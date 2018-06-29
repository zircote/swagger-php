<?php
namespace OpenApi\LinkExample;

/**
 * @OA\Schema(schema="repository", type="object")
 */
class Repository
{

    /**
     * @OA\Property()
     * @var string
     */
    public $slug;

    /**
     * @OA\Property()
     * @var User
     */
    public $owner;
}
