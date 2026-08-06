<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'repository')]
class RepositorySpec
{
}

#[OA\Info(
    title: 'Null Ref',
    version: '1.0'
)]
class NullRefSpec
{
    #[OA\Operation\Get(
        path: '/api/refonly',
        operationId: 'refonly',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Ref response',
                content: new OA\MediaType\Json(
                    ref: '#/components/schemas/repository',
                    schema: new OA\Schema(nullable: true),
                )
            ),
        ]
    )]
    public function refonly()
    {
    }

    #[OA\Operation\Get(
        path: '/api/refplus',
        operationId: 'refplus',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Ref plus response',
                content: new OA\MediaType\Json(
                    ref: '#/components/schemas/repository',
                    schema: new OA\Schema(
                    description: 'The repository',
                    nullable: true,
                    ),
                )
            ),
        ]
    )]
    public function refplusy()
    {
    }
}
