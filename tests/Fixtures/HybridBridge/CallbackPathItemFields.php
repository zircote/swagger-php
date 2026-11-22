<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\HybridBridge;

use OpenApi\Attributes as OAT;

/**
 * A component callback with a field of its path item, which a `Callback` has no place for.
 */
#[OAT\Info(title: 'Callback path item fields', version: '1.0')]
#[OAT\Components(callbacks: [
    'onEvent' => [
        '{$request.body#/url}' => [
            'summary' => 'Not kept by a Callback',
            'post' => ['responses' => ['200' => ['description' => 'Received']]],
        ],
    ],
])]
class CallbackPathItemFields
{
}
