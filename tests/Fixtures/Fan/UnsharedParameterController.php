<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Fan;

use OpenApi\Spec as OA;

class UnsharedParameterController
{
    #[OA\Operation\Put(path: '/pets/{id}')]
    #[OA\Operation\Delete(path: '/pets/{id}')]
    public function change(
        #[OA\Parameter\Path(name: 'id')]
        int $id,
    ): void {
    }
}
