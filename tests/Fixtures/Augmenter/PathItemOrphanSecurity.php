<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Security\Requirement(scheme: 'bearerAuth')]
class PathItemOrphanSecurity
{
    #[OA\Operation\Get(path: '/orphan')]
    #[OA\Response(response: 200, description: 'OK')]
    public function index()
    {
    }
}
