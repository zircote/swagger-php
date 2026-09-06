<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

class SuppressedController
{
    /**
     * Get a thing.
     *
     * Returns the thing by ID.
     *
     * @param int $id the thing identifier
     */
    #[OA\Operation\Get(path: '/things/{id}', summary: null, description: null)]
    #[OA\Response(response: 200, description: 'OK')]
    public function getThing(
        #[OA\Parameter\Path(name: 'id', description: null)]
        int $id,
    ) {
    }
}
