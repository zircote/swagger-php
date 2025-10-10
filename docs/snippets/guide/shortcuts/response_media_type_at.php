<?php

use OpenApi\Attributes as OA;

class Controller
{
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        content: new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(ref: '#/components/schemas/User'),
        ),
    )]
    public function endpoint()
    {
    }
}
