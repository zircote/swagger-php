<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\MemberErrors;

use OpenApi\Spec as OA;

/**
 * Its own method is ambiguous, and a child under a path item reads it again.
 */
abstract class AbstractController
{
    #[OA\Operation\Put(path: '/items/{id}')]
    #[OA\Operation\Delete(path: '/items/{id}')]
    public function change(
        #[OA\Parameter\Path(name: 'id')]
        int $id,
    ): void {
    }

    #[OA\Operation\Get(path: '/items')]
    #[OA\Response(response: 200, description: 'The items')]
    public function list(): void
    {
    }
}
