<?php

namespace OpenApi\Examples\OpenapiSpec;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="repository")
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
