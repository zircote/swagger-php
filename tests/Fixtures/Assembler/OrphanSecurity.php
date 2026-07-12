<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;

#[OA\Security\Requirement(scheme: 'bearerAuth')]
class OrphanSecurity
{
    #[OA\Operation\Get(path: '/orphan')]
    #[OA\Response(response: 200, description: 'OK')]
    public function index()
    {
    }
}
