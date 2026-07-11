<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Webhooks\Spec;

use OpenApi\Spec as OA;

class NewPetWebhook
{
    #[OA\Operation(
        webhook: 'newPet',
        method: 'post',
        operationId: 'new-pet',
        requestBody: new OA\RequestBody(
            description: 'Information about a new pet in the system',
            content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: Pet::class))],
        ),
        responses: [
            new OA\Response(response: 200, description: 'Return a 200 status to indicate that the data was received successfully'),
        ],
    )]
    public function newPet()
    {
    }
}
