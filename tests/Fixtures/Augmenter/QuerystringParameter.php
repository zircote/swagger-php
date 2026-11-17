<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

class QuerystringParameter
{
    #[OA\Operation\Get(path: '/pets')]
    #[OA\Response(response: 200, description: 'OK')]
    public function search(
        #[OA\Parameter\Querystring(content: new OA\MediaType(mediaType: 'application/x-www-form-urlencoded'))]
        array $search,
    ) {
    }
}
