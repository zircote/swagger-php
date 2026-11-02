<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

class PlainPathParameters
{
    #[OA\Operation\Get(path: '/pets/{id}/{slug}')]
    #[OA\Response(response: 200, description: 'OK')]
    public function show(
        #[OA\Parameter(in: 'path')]
        ?int $id,
        #[OA\Parameter(in: 'path')]
        string $slug = 'all',
    ) {
    }
}
