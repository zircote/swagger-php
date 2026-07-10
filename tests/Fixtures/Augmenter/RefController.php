<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

class RefController
{
    #[OA\Operation\Get(path: '/ref-targets')]
    #[OA\Response(response: 200, description: 'OK', content: [
        new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: RefTarget::class)),
    ])]
    public function list()
    {
    }
}
