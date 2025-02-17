<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingLinks\Attributes;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'pullrequest')]
class PullRequest
{
    /**
     * @var int
     */
    #[OAT\Property(type: 'integer')]
    public $id;

    /**
     * @var string
     */
    #[OAT\Property(type: 'string')]
    public $title;

    /**
     * @var Repository
     */
    #[OAT\Property(ref: Repository::class)]
    public $repository;

    /**
     * @var User
     */
    #[OAT\Property(ref: User::class)]
    public $author;

    public function __construct(
        #[OAT\Property()]
        public State $state
    ) {
    }
}
