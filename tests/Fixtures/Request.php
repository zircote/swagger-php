<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

// NOTE: this file uses "\r\n" linebreaks on purpose

namespace OpenApi\Tests\Fixtures;

use OpenApi\Annotations as OA;
use OpenApi\Tests\Fixtures;

/**
 * @OA\RequestBody
 */
class Request
{
    /**
     * @OA\Post(
     *     path="/",
     *     @OA\RequestBody(ref=Fixtures\Request::class),
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    public function post()
    {

    }
}
