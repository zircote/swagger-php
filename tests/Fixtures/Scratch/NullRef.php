<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'repository')]
class Repository
{
}

#[OAT\Info(
    title: 'Null Ref',
    version: '1.0'
)]
class NullRef
{
    #[OAT\Get(
        path: '/api/refonly',
        operationId: 'refonly',
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Ref response',
                content: new OAT\JsonContent(
                    ref: '#/components/schemas/repository',
                    nullable: true
                )
            ),
        ]
    )]
    public function refonly()
    {
    }

    #[OAT\Get(
        path: '/api/refplus',
        operationId: 'refplus',
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Ref plus response',
                content: new OAT\JsonContent(
                    ref: '#/components/schemas/repository',
                    description: 'The repository',
                    nullable: true
                )
            ),
        ]
    )]
    public function refplusy()
    {
    }

    #[OAT\Get(
        path: '/api/annotated',
        operationId: 'annotated',
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Annotated refs response',
                content: new OAT\JsonContent(ref: NullRefAnnotated::class)
            ),
        ]
    )]
    public function annotated()
    {
    }
}

// a `$ref` property keeps its annotations: on the wrapper when nullable, beside `$ref` in 3.1
#[OAT\Schema(schema: 'annotatedRefs')]
class NullRefAnnotated
{
    #[OAT\Property(title: 'Nullable', description: 'A nullable reference', default: 'none', example: 'zircote/swagger-php', deprecated: true, readOnly: true)]
    public ?Repository $nullableRepository;

    #[OAT\Property(title: 'Plain', description: 'A plain reference', default: 'none', example: 'zircote/swagger-php', deprecated: true, readOnly: true)]
    public Repository $repository;

    #[OAT\Property(examples: ['zircote/swagger-php', 'DerManoMann/openapi-extras'], writeOnly: true)]
    public ?Repository $nullableListed;

    #[OAT\Property(examples: ['zircote/swagger-php', 'DerManoMann/openapi-extras'], writeOnly: true)]
    public Repository $listed;
}
