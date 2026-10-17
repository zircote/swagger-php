<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Fan;

use OpenApi\Spec as OA;

class MixedTargets
{
    #[OA\Operation\Post(path: '/pets')]
    #[OA\RequestBody(description: 'The pet')]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\MediaType(mediaType: 'application/json', fan: OA\Fan::All)]
    public function create(): void
    {
    }
}
