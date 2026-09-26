<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

class PathItemContractController implements PathItemContract
{
    #[OA\Operation\Get(path: '/signatures')]
    #[OA\Response(response: 200, description: 'Signatures')]
    public function signatures()
    {
    }
}
