<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\HybridBridge;

use OpenApi\Attributes as OAT;

/**
 * An example and a webhook declared on a class, outside any `Components` or `OpenApi`.
 */
#[OAT\Info(version: '1.0.0', title: 'Class-Level Components')]
#[OAT\Examples(example: 'ConfigError', summary: 'Configuration error', value: ['status' => 500])]
#[OAT\Webhook(
    webhook: 'configFailed',
    post: new OAT\Post(
        operationId: 'configFailed',
        requestBody: new OAT\RequestBody(content: new OAT\JsonContent(
            examples: [new OAT\Examples(example: 'error', ref: '#/components/examples/ConfigError')],
        )),
        responses: [new OAT\Response(response: 200, description: 'ok')],
    ),
)]
class ClassLevelComponents
{
}
