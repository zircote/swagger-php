<?php

namespace OpenApi\Examples\OpenapiSpecAttributes;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="pullrequest")
 */
#[OA\Schema(schema: 'pullrequest')]
class PullRequest
{

    /**
     * @OA\Property()
     * @var integer
     */
    #[OA\Property(type: 'integer')]
    public $id;

    /**
     * @OA\Property()
     * @var string
     */
    #[OA\Property(type: 'string')]
    public $title;

    /**
     * @OA\Property()
     * @var Repository
     */
    #[OA\Property(ref: '#/components/schemas/repository')]
    public $repository;

    /**
     * @OA\Property()
     * @var User
     */
    #[OA\Property(ref: '#/components/schemas/user')]
    public $author;
}
