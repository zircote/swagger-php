<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Non-public method attributes', version: '1.0')]
class NonPublicMethodAttributesController
{
    #[OA\Operation\Get(path: '/public', operationId: 'onPublic')]
    #[OA\Response(response: 200, description: 'OK')]
    public function index(): void
    {
    }

    #[OA\Operation\Get(path: '/protected', operationId: 'onProtected')]
    #[OA\Response(response: 200, description: 'OK')]
    protected function load(): void
    {
    }

    #[OA\Operation\Get(path: '/private', operationId: 'onPrivate')]
    #[OA\Response(response: 200, description: 'OK')]
    private static function loadStats(): void
    {
    }

    private function helper(): void
    {
    }
}
