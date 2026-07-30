<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

class JsonController
{
    #[OA\Operation\Post(path: '/json')]
    #[OA\RequestBody(content: [new OA\MediaType\Json(type: 'string')])]
    #[OA\Response(response: 200, description: 'OK')]
    public function json()
    {
    }
}
