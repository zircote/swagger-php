<?php declare(strict_types=1);

namespace OpenApi\Examples\Webhooks81;

use OpenApi\Attributes as OAT;

#[OAT\OpenApi(
    info: new OAT\Info(version: '1.0.0', title: 'Webhook Example'),
    webhooks: [
        new OAT\Webhook(
            webhook: 'newPet',
            path: new OAT\PathItem(
                post: new OAT\Post(
                    requestBody: new OAT\RequestBody(
                        description: 'Information about a new pet in the system',
                        content: new OAT\MediaType(
                            mediaType: 'application/json',
                            schema: new OAT\Schema(ref: Pet::class)
                        )
                    ),
                    responses: [
                        new OAT\Response(
                            response: 200,
                            description: 'Return a 200 status to indicate that the data was received successfully'
                        ),
                    ]
                )
            )
        ),
    ],
)]
class OpenApiSpec
{
}
