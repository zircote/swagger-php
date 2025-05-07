<?php

use OpenApi\Attributes as OA;

#[OA\Schema(
    properties: [
        'name' => new OA\Property(property: 'name', type: 'string'),
        'email' => new OA\Property(property: 'email', type: 'string'),
    ]
)]
class User {}
