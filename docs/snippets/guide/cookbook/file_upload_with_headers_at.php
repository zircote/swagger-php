<?php

use OpenApi\Attributes as OA;

class Controller
{
    #[OA\Post(
        path: "/v1/media/upload",
        summary: "Upload document",
        description: "",
        tags: ["Media"],
        body: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/octet-stream",
                schema: new OA\Schema(
                    required: ["content"],
                    properties: [
                        new OA\Property(
                            description: "Binary content of file",
                            property: "content",
                            type: "string",
                            format: "binary",
                        ),
                    ],
                ),
            ),
        ),
    )]
    #[OA\Response(
        response: 400,
        description: "Bad Request",
    )]
    public function upload()
    {
        // ...
    }
}
