<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

// summary exists as of 3.1; the 3.0 document omits it
#[OAT\Info(
    version: '1.0',
    title: 'InfoObject',
    summary: 'A short summary of the API',
    description: 'What the API is for, at more length.',
    termsOfService: 'https://example.com/terms',
    contact: new OAT\Contact(name: 'Support', url: 'https://example.com/support', email: 'support@example.com'),
    license: new OAT\License(name: 'MIT'),
)]
class InfoObjectController
{
    #[OAT\Get(
        path: '/endpoint',
        operationId: 'endpoint',
        responses: [
            new OAT\Response(response: 200, description: 'OK'),
        ],
    )]
    public function endpoint(): void
    {
    }
}
