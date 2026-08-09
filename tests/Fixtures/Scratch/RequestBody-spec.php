<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'RequestBodySchema')]
class RequestBodySchemaSpec
{
}

#[OA\Components]
#[OA\RequestBody(request: 'RequestBodyRef')]
class RequestBodyRefSpec
{
}

#[OA\Components]
#[OA\RequestBody(request: 'foo')]
class RequestBodyRefFooSpec
{
}

#[OA\Info(title: 'RequestBody', version: '1.0')]
class RequestBodyControllerSpec
{
    #[OA\Operation\Post(
        path: '/endpoint/schema-ref-json',
        operationId: 'postSchemaRefJson',
        requestBody: new OA\RequestBody(
            description: 'Information about a new pet in the system',
            content: new OA\MediaType\Json(ref: RequestBodySchemaSpec::class),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'All good'
            ),
        ]
    )]
    public function postSchemaRefJson()
    {
    }

    #[OA\Operation\Post(
        path: '/endpoint/schema-ref',
        operationId: 'postSchemaRef',
        requestBody: new OA\RequestBody(
            description: 'Information about a new pet in the system',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(ref: RequestBodySchemaSpec::class)
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'All good'
            ),
        ]
    )]
    public function postSchemaRef()
    {
    }

    #[OA\Operation\Post(
        path: '/endpoint/ref',
        operationId: 'postRef',
        requestBody: new OA\RequestBody(ref: RequestBodyRefSpec::class),
        responses: [
            new OA\Response(
                response: 200,
                description: 'All good'
            ),
        ]
    )]
    public function postRef()
    {
    }

    #[OA\Operation\Post(
        path: '/endpoint/ref-foo',
        operationId: 'postRefFoo',
        responses: [
            new OA\Response(
                response: 200,
                description: 'All good'
            ),
        ]
    )]
    public function postRefFoo(#[OA\RequestBody] RequestBodyRefFooSpec $body)
    {
    }
}
