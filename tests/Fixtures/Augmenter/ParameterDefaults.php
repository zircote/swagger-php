<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

class ParameterDefaults
{
    #[OA\Operation\Get(path: '/pets/{id}')]
    #[OA\Response(response: 200, description: 'OK')]
    public function list(
        #[OA\Parameter\Query]
        int $limit,
        #[OA\Parameter\Path]
        int $id = 0,
        #[OA\Parameter\Query]
        int $page = 1,
        #[OA\Parameter\Query(required: true)]
        string $sort = 'name',
    ) {
    }
}
