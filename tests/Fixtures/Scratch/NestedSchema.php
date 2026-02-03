<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class NestedSchemaOne
{
}

#[OAT\Schema]
class NestedSchemaTwo
{
}

#[OAT\Schema]
class MultipleOneOf
{
    /**
     * @param array<MultipleOneOf|NestedSchemaTwo> $values
     */
    public function __construct(
        #[OAT\Property(items: new OAT\Items(oneOf: [
            new OAT\Schema(type: MultipleOneOf::class, description: 'Recursive nested item'),
            new OAT\Schema(type: NestedSchemaTwo::class, description: 'Another item'),
        ]))]
        public array $values
    ) {
    }
}

#[OAT\Info(
    title: 'Parameter Content Scratch',
    version: '1.0'
)]
#[OAT\Schema(
    required: ['errors'],
    properties: [
        new OAT\Property(
            property: 'errors',
            description: 'Validation errors',
            type: 'object',
            minItems: 1,
            uniqueItems: true,
            additionalProperties: new OAT\AdditionalProperties(
                description: 'Array of error messages for property',
                type: ['array'],
                items: new OAT\Items(
                    type: 'string',
                ),
                minItems: 1,
                uniqueItems: true,
            ),
        ),
    ],
    type: 'object'
)]
class NestedSchema
{
}

class NestedSchemaController
{
    #[OAT\Post(
        path: '/api/post',
        operationId: 'post',
        requestBody: new OAT\RequestBody(content: [new OAT\MediaType(
            mediaType: 'application/json',
            schema: new OAT\Schema(
                required: ['note'],
                properties: [
                    new OAT\Property(property: 'note', example: 'My note'),
                    new OAT\Property(
                        property: 'other',
                        description: 'other',
                        oneOf: [
                            new OAT\Schema(type: NestedSchemaOne::class),
                            new OAT\Schema(type: NestedSchemaTwo::class),
                        ]
                    ),
                ]
            )
        )]),
        responses: [new OAT\Response(response: 200, description: 'OK')]
    )]
    public function post()
    {

    }

    /**
     * @param string[] $tags
     */
    #[OAT\Get(
        path: '/api/get',
        operationId: 'get',
    )]
    #[OAT\Response(response: 200, description: 'successful operation')]
    public function get(
        #[OAT\QueryParameter(
            schema: new OAT\Schema(
                type: 'array',
                items: new OAT\Items(type: 'string')
            )
        )] array $tags,
    ) {
    }

    #[OAT\Put(
        path: '/api/put',
        operationId: 'put',
    )]
    #[OAT\Response(response: 200, description: 'successful operation')]
    public function put(
        #[OAT\QueryParameter(
            schema: new OAT\Schema(
                type: 'array',
                items: new OAT\Items(type: 'string')
            )
        )] array $tags,
    ) {
    }

    /**
     * @param string[] $tags
     */
    #[OAT\Delete(
        path: '/api/delete',
        operationId: 'delete',
    )]
    #[OAT\Response(response: 200, description: 'successful operation')]
    public function delete(
        #[OAT\QueryParameter] array $tags,
    ) {
    }
}
