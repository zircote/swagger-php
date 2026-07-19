<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Spec;

use OpenApi\Spec as OA;

trait DeleteEntity
{
    #[OA\Operation\Delete(
        path: '/entities/{id}',
        operationId: 'deleteEntity',
        tags: ['Entities'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of entity to delete',
                required: true,
                schema: new OA\Schema(type: 'string'),
            ),
        ],
        responses: [
            new OA\Response(response: 'default', description: 'successful operation'),
        ],
    )]
    public function deleteEntity($id)
    {
    }
}
