<?php

use OpenApi\Attributes as OA;

class OpenApiSpec
{
    #[OA\Post(
        path: '/v1/user/update',
        summary: 'Form post',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'name',
                        ),
                        new OA\Property(
                            description: 'file to upload',
                            property: 'avatar',
                            type: 'string',
                            format: 'binary',
                        ),
                    ],
                ),
            ),
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
