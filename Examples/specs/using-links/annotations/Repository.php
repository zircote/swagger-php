<?php

namespace OpenApi\Examples\Specs\UsingLinks\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="repository")
 */
class Repository
{
    /**
     * @OA\Property
     *
     * @var string
     */
    public $slug;

    /**
     * @OA\Property
     *
     * @var User
     */
    public $owner;
}
