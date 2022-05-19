<?php

use OpenApi\Attributes as OA;

#[OA\Info(title: "My First API", version: "0.1")]
class OpenApi {}

class MyController {

    #[OA\Get(path: '/api/data.json')]
    #[OA\Response(response: '200', description: 'The data')]
    public function getResource() {
        // ...
    }
}
