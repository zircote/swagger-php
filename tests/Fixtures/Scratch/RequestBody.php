<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\RequestBody()]
class RequestBodyRef
{
}

#[OAT\RequestBody(request: 'foo')]
class RequestBodyRefFoo
{
}

#[OAT\Info(title: 'RequestBody', version: '1.0')]
class RequestBodyController
{
    #[OAT\Get(
        path: '/endpoint',
        requestBody: new OAT\RequestBody(
            description: 'Information about a new pet in the system',
            content: new OAT\MediaType(
                mediaType: 'application/json'
            ),
        ),
        responses: [
            new OAT\Response(
                response: 200,
                description: 'All good'
            ),
        ]
    )]
    public function post()
    {
    }

    #[OAT\Post(
        path: '/endpoint/ref',
        requestBody: new OAT\RequestBody(ref: RequestBodyRef::class),
        responses: [
            new OAT\Response(
                response: 200,
                description: 'All good'
            ),
        ]
    )]
    public function postRef()
    {
    }

    #[OAT\Post(
        path: '/endpoint/ref-foo',
        requestBody: new OAT\RequestBody(ref: RequestBodyRefFoo::class),
        responses: [
            new OAT\Response(
                response: 200,
                description: 'All good'
            ),
        ]
    )]
    public function postRefFoo()
    {
    }
}
