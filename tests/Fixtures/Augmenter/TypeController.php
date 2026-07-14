<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

class TypeController
{
    #[OA\Operation\Get(path: '/typed/{id}')]
    #[OA\Response(response: 200, description: 'OK')]
    public function getTyped(
        #[OA\Parameter\Path(name: 'id')]
        int $id,
        #[OA\Parameter\Query]
        ?string $filter = null,
    ) {
    }
}
