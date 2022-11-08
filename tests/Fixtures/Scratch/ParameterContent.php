<?php declare(strict_types=1);

use OpenApi\Attributes as OAT;

/**
 * An API endpoint.
 */
#[OAT\Info(
    title: 'Parameter Content Scratch',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
    tags: ['endpoints'],
    description: 'An endpoint',
    operationId: 'endpoint',
    parameters: [
        new OAT\Parameter(
            name: 'filter',
            in: 'query',
            content: new OAT\JsonContent(
                properties: [
                    new OAT\Property(property: 'type', type: 'string'),
                    new OAT\Property(property: 'color', type: 'string'),
                ]
            )
        ),
    ],
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class Endpoint
{
}
