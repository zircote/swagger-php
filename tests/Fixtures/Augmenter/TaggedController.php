<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Tag(name: 'users', description: 'Everything about users')]
class TaggedController
{
    #[OA\Tag(name: 'admin')]
    #[OA\Operation\Get(path: '/users', tags: ['list'])]
    #[OA\Response(response: 200, description: 'OK')]
    public function list(): void
    {
    }

    #[OA\Tag(name: 'admin')]
    #[OA\Operation\Delete(path: '/users/{id}')]
    #[OA\Response(response: 204, description: 'Gone')]
    public function delete(): void
    {
    }
}
