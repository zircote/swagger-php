<?php

use OpenApi\Attributes as OA;

#[OA\Get(
    // ...
    callbacks: [
        'onChange' => [
            '{$request.query.callbackUrl}' => [
                'post' => [
                    'requestBody' => new OA\RequestBody(
                        description: 'subscription payload',
                        content: [
                            new OA\MediaType(
                                mediaType: 'application/json',
                                schema: new OA\Schema(
                                    properties: [
                                        new OA\Property(
                                            property: 'timestamp',
                                            type: 'string',
                                            format: 'date-time',
                                            description: 'time of change'
                                        ),
                                    ],
                                ),
                            ),
                        ],
                    ),
                ],
            ],
        ],
    ],
    // ...
)]
class Controller {}
