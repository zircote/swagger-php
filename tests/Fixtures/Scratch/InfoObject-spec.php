<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

// summary exists as of 3.1; the 3.0 document omits it.
// Stacked siblings: Contact and License merge into the Info.
#[OA\Info(
    version: '1.0',
    title: 'InfoObject',
    summary: 'A short summary of the API',
    description: 'What the API is for, at more length.',
    termsOfService: 'https://example.com/terms',
)]
#[OA\Contact(name: 'Support', url: 'https://example.com/support', email: 'support@example.com')]
#[OA\License(name: 'MIT')]
class InfoObjectControllerSpec
{
    #[OA\Operation\Get(path: '/endpoint', operationId: 'endpoint')]
    #[OA\Response(response: 200, description: 'OK')]
    public function endpoint(): void
    {
    }
}
