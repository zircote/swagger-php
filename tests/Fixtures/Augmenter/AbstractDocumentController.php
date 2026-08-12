<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

abstract class AbstractDocumentController
{
    #[OA\Operation\Get(path: '')]
    #[OA\Response(response: 200, description: 'List documents')]
    public function getDocuments(): void
    {
    }
}
