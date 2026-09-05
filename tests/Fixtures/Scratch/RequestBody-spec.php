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
    // Stacked siblings: Json -> RequestBody -> Post. The responses stay inline
    // because MediaType merges into Response and RequestBody alike — with both
    // as siblings the merge would be ambiguous.
    #[OA\MediaType\Json(ref: RequestBodySchemaSpec::class)]
    #[OA\RequestBody(description: 'Information about a new pet in the system')]
    #[OA\Operation\Post(
        path: '/endpoint/schema-ref-json',
        operationId: 'postSchemaRefJson',
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

    // As above, one level deeper: Schema -> MediaType -> RequestBody -> Post.
    #[OA\Schema(ref: RequestBodySchemaSpec::class)]
    #[OA\MediaType(mediaType: 'application/json')]
    #[OA\RequestBody(description: 'Information about a new pet in the system')]
    #[OA\Operation\Post(
        path: '/endpoint/schema-ref',
        operationId: 'postSchemaRef',
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
