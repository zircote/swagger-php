<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Petstore30\Models;

/**
 * Class ApiResponse.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @OA\Schema(
 *     description="Api response",
 *     title="Api response"
 * )
 */
class ApiResponse
{
    /**
     * @OA\Property(
     *     description="Code",
     *     title="Code",
     *     format="int32"
     * )
     *
     * @var int
     */
    private $code;

    /**
     * OA\Property(
     *    description="Type",
     *    title="Type",
     * ).
     *
     * @var string
     */
    private $type;

    /**
     * @OA\Property(
     *     description="Message",
     *     title="Message"
     * )
     *
     * @var string
     */
    private $message;
}
