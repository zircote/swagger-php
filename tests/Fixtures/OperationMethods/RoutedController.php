<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\OperationMethods;

use OpenApi\Spec as OA;

/**
 * No operation of its own: `RouteTranslator` gives `update()` one.
 */
#[OA\Info(title: 'Routed', version: '1.0')]
class RoutedController
{
    #[OA\Response(response: 204, description: 'Updated')]
    public function update(
        #[OA\Parameter\Path(name: 'id')]
        int $id,
    ): void {
    }
}
