<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\HybridBridge;

use OpenApi\Attributes as OAT;

/**
 * An operation nested in a `PathItem` carries no path of its own — it belongs to the
 * `PathItem` — so the bridge has to take it from the parent.
 */
#[OAT\Info(version: '1.0.0', title: 'Nested PathItem')]
#[OAT\PathItem(
    path: '/nested',
    get: new OAT\Get(
        operationId: 'nestedGet',
        responses: [new OAT\Response(response: 200, description: 'ok')],
    ),
)]
class NestedPathItem
{
}
