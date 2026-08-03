<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'NestedSchemaOne')]
class NestedSchemaOneSpec
{
}

#[OA\Schema(schema: 'NestedSchemaTwo')]
class NestedSchemaTwoSpec
{
}

#[OA\Schema(schema: 'MultipleOneOf')]
class MultipleOneOfSpec
{
    /**
     * @param array<MultipleOneOfSpec|NestedSchemaTwoSpec> $values
     */
    public function __construct(
        #[OA\Property]
        #[OA\Schema\Items(oneOf: [
            new OA\Schema(ref: MultipleOneOfSpec::class, description: 'Recursive nested item'),
            new OA\Schema(ref: NestedSchemaTwoSpec::class, description: 'Another item'),
        ])]
        public array $values
    ) {
    }
}

#[OA\Info(
    title: 'Parameter Content Scratch',
    version: '1.0'
)]
#[OA\Schema(
    schema: 'NestedSchema',
    required: ['errors'],
    properties: [
        new OA\Property(
            property: 'errors',
            schema: new OA\Schema(
                description: 'Validation errors',
                type: 'object',
                minItems: 1,
                uniqueItems: true,
                additionalProperties: new OA\Schema\AdditionalProperties(
                    description: 'Array of error messages for property',
                    type: ['array'],
                    items: new OA\Schema\Items(
                        type: 'string',
                    ),
                    minItems: 1,
                    uniqueItems: true,
                ),
            ),
        ),
    ],
    type: 'object'
)]
class NestedSchemaSpec
{
}

class NestedSchemaControllerSpec
{
    #[OA\Operation\Post(
        path: '/api/post',
        operationId: 'post',
        requestBody: new OA\RequestBody(content: [new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(
                required: ['note'],
                properties: [
                    new OA\Property(property: 'note', schema: new OA\Schema(example: 'My note')),
                    new OA\Property(
                        property: 'other',
                        schema: new OA\Schema(
                            description: 'other',
                            oneOf: [
                                new OA\Schema(ref: NestedSchemaOneSpec::class),
                                new OA\Schema(ref: NestedSchemaTwoSpec::class),
                            ]
                        ),
                    ),
                ]
            )
        )]),
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function post()
    {

    }

    /**
     * @param string[] $tags
     */
    #[OA\Operation\Get(
        path: '/api/get',
        operationId: 'get',
    )]
    #[OA\Response(response: 200, description: 'successful operation')]
    public function get(
        #[OA\Parameter\Query(
            schema: new OA\Schema\Items(type: 'string')
        )] array $tags,
    ) {
    }

    #[OA\Operation\Put(
        path: '/api/put',
        operationId: 'put',
    )]
    #[OA\Response(response: 200, description: 'successful operation')]
    public function put(
        #[OA\Parameter\Query(
            schema: new OA\Schema\Items(type: 'string')
        )] array $tags,
    ) {
    }

    /**
     * @param string[] $tags
     */
    #[OA\Operation\Delete(
        path: '/api/delete',
        operationId: 'delete',
    )]
    #[OA\Response(response: 200, description: 'successful operation')]
    public function delete(
        #[OA\Parameter\Query] array $tags,
    ) {
    }
}
