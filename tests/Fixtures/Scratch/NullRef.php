<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

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
}
