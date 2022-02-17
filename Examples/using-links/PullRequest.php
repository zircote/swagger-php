<?php

namespace OpenApi\Examples\UsingLinks;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="pullrequest")
 */
class PullRequest
{

    /**
     * @OA\Property
     *
     * @var int
     */
    public $id;

    /**
     * @OA\Property
     *
     * @var string
     */
    public $title;

    /**
     * @OA\Property
     *
     * @var Repository
     */
    public $repository;

    /**
     * @OA\Property
     *
     * @var User
     */
    public $author;
}
