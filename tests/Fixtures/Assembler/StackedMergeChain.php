<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;

class StackedMergeChain
{
    #[OA\Operation\Get(path: '/outer-first')]
    #[OA\Response(response: 200, description: 'OK')]
    #[OA\MediaType(mediaType: 'application/json')]
    #[OA\Schema(type: 'string')]
    public function outerFirst()
    {
    }

    #[OA\Schema(type: 'string')]
    #[OA\MediaType(mediaType: 'application/json')]
    #[OA\Response(response: 200, description: 'OK')]
    #[OA\Operation\Get(path: '/inner-first')]
    public function innerFirst()
    {
    }

    #[OA\Response(response: 200, description: 'OK')]
    #[OA\Operation\Get(path: '/mixed')]
    #[OA\Schema(type: 'string')]
    #[OA\MediaType(mediaType: 'application/json')]
    public function mixed()
    {
    }
}
