<?php

namespace Openapi\Snippets\Cookbook\UploadingMultipartFormData;

use OpenApi\Spec as OA;

class OpenApiSpec
{
    #[OA\Operation\Post(
        path: '/v1/user/update',
        summary: 'Form post',
        requestBody: new OA\RequestBody(
            content: [new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'name',
                            schema: new OA\Schema(description: 'the file name')
                        ),
                        new OA\Property(
                            property: 'avatar',
                            schema: new OA\Schema(
                                description: 'file to upload',
                                type: 'string',
                                format: 'binary',
                            ),
                        ),
                    ],
                ),
            )],
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Success'
    )]
    public function update()
    {
        // ...
    }
}
