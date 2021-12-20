<?php

namespace OpenApi\Examples\OpenapiSpecAttributes;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'pullrequest')]
class PullRequest
{

    /**
     * @var integer
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
    #[OAT\Property(ref: '#/components/schemas/repository')]
    public $repository;

    /**
     * @var User
     */
    #[OAT\Property(ref: '#/components/schemas/user')]
    public $author;
}
