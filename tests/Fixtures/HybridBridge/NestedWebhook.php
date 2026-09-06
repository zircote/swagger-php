<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\HybridBridge;

use OpenApi\Attributes as OAT;

/**
 * A webhook's operation is collected by the webhook that owns it. Collecting it a second
 * time as a flat operation yields a copy with neither path nor webhook.
 */
#[OAT\OpenApi(
    info: new OAT\Info(version: '1.0.0', title: 'Nested Webhook'),
    webhooks: [
        new OAT\Webhook(
            webhook: 'newPet',
            post: new OAT\Post(
                operationId: 'newPet',
                responses: [new OAT\Response(response: 200, description: 'ok')],
            ),
        ),
    ],
)]
class NestedWebhook
{
}
