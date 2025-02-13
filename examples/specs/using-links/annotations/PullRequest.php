<?php

namespace OpenApi\Examples\Specs\UsingLinks\Annotations;

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

    public function __construct(
        /**
         * @OA\Property(
         *     ref="OpenApi\Examples\Specs\UsingLinks\Annotations\State"
         * )
         */
        public string $state
    )
    {
    }
}
