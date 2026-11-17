<?php

namespace Openapi\Snippets\Cookbook\MultipartPartsByPosition;

use OpenApi\Spec as OA;

class OpenApiSpec
{
    #[OA\Operation\Post(
        path: '/v1/photos',
        summary: 'A JSON manifest, then the photos',
        requestBody: new OA\RequestBody(
            content: [new OA\MediaType(
                mediaType: 'multipart/mixed',
                schema: new OA\Schema\Items(
                    prefixItems: [new OA\Schema(type: 'object')],
                    contentMediaType: 'image/jpeg',
                ),
                prefixEncoding: [new OA\Encoding(contentType: 'application/json')],
                itemEncoding: new OA\Encoding(contentType: 'image/jpeg'),
            )],
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Success'
    )]
    public function upload()
    {
        // ...
    }

    #[OA\Operation\Post(
        path: '/v1/albums',
        summary: 'Form post with the same parts in one field',
        requestBody: new OA\RequestBody(
            content: [new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'title',
                            schema: new OA\Schema(type: 'string')
                        ),
                        new OA\Property(
                            property: 'photos',
                            schema: new OA\Schema\Items(
                                prefixItems: [new OA\Schema(type: 'object')],
                                contentMediaType: 'image/jpeg',
                            ),
                        ),
                    ],
                ),
                encoding: [new OA\Encoding(
                    encoding: 'photos',
                    contentType: 'multipart/mixed',
                    prefixEncoding: [new OA\Encoding(contentType: 'application/json')],
                    itemEncoding: new OA\Encoding(contentType: 'image/jpeg'),
                )],
            )],
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Success'
    )]
    public function create()
    {
        // ...
    }
}
