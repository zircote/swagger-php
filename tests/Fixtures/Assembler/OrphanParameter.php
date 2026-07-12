<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;

#[OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'integer'))]
class OrphanParameter
{
    #[OA\Operation\Get(path: '/orphan')]
    #[OA\Response(response: 200, description: 'OK')]
    public function index()
    {
    }
}
