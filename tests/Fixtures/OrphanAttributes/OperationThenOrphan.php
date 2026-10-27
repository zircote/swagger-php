<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\OrphanAttributes;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Orphan attributes', version: '1.0')]
class OperationThenOrphan
{
    #[OA\Operation\Get(path: '/pets', operationId: 'listPets')]
    #[OA\Response(response: 200, description: 'The pets')]
    public function list(): void
    {
    }

    #[OA\Response(response: 422, description: 'Invalid')]
    public function unrouted(): void
    {
    }
}
