<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'repository')]
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
    )]
    #[OA\Response(
        response: 200,
        description: 'Ref response',
        content: new OA\MediaType\Json(
            ref: '#/components/schemas/repository',
            schema: new OA\Schema(nullable: true),
        )
    )]
    public function refonly()
    {
    }

    #[OA\Operation\Get(
        path: '/api/refplus',
        operationId: 'refplus',
    )]
    #[OA\Response(
        response: 200,
        description: 'Ref plus response',
        content: new OA\MediaType\Json(
            ref: '#/components/schemas/repository',
            schema: new OA\Schema(
                description: 'The repository',
                nullable: true,
            ),
        )
    )]
    public function refplusy()
    {
    }

    #[OA\Operation\Get(
        path: '/api/annotated',
        operationId: 'annotated',
    )]
    #[OA\Response(
        response: 200,
        description: 'Annotated refs response',
        content: new OA\MediaType\Json(ref: NullRefAnnotatedSpec::class)
    )]
    public function annotated()
    {
    }
}

// a `$ref` property keeps its annotations: on the wrapper when nullable, beside `$ref` in 3.1
#[OA\Schema(component: 'annotatedRefs')]
class NullRefAnnotatedSpec
{
    #[OA\Property(schema: new OA\Schema(title: 'Nullable', description: 'A nullable reference', default: 'none', example: 'zircote/swagger-php', deprecated: true, readOnly: true))]
    public ?RepositorySpec $nullableRepository;

    #[OA\Property(schema: new OA\Schema(title: 'Plain', description: 'A plain reference', default: 'none', example: 'zircote/swagger-php', deprecated: true, readOnly: true))]
    public RepositorySpec $repository;
}
