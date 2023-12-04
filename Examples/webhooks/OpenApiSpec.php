<?php declare(strict_types=1);

namespace OpenApi\Examples\Webhooks;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Webhook Example"
 *     ),
 *     @OA\Webhook(
 *         webhook="newPet",
 *         @OA\PathItem(
 *             @OA\Post(
 *                 @OA\RequestBody(
 *                     description="Information about a new pet in the system",
 *                         @OA\MediaType(
 *                             mediaType="application/json",
 *                             @OA\Schema(ref="#/components/schemas/Pet")
 *                         ),
 *                     ),
 *                     @OA\Response(
 *                         response=200,
 *                         description="Return a 200 status to indicate that the data was received successfully"
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class OpenApiSpec
{
}
