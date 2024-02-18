<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

// NOTE: this file uses "\r\n" linebreaks on purpose

namespace OpenApi\Tests\Fixtures;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody
 */
class Request
{
    /**
     * @OA\Post(
     *     path="/",
     *     @OA\RequestBody(ref=OpenApi\Tests\Fixtures\Request::class),
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    public function post()
    {

    }
}
