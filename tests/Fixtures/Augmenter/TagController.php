<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

class TagController
{
    #[OA\Operation\Get(path: '/tagged', tags: ['alpha', 'beta'])]
    #[OA\Response(response: 200, description: 'OK')]
    public function tagged()
    {
    }
}
