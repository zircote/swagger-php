<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingLinks\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'pullrequest')]
class PullRequest
{
    #[OA\Property]
    #[OA\Schema(type: 'integer')]
    public $id;

    #[OA\Property]
    #[OA\Schema(type: 'string')]
    public $title;

    #[OA\Property]
    #[OA\Schema(ref: Repository::class)]
    public $repository;

    #[OA\Property]
    #[OA\Schema(ref: User::class)]
    public $author;

    public function __construct(
        #[OA\Property]
        public State $state,
    ) {
    }
}
