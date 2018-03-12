<?php

/**
 * @license Apache 2.0
 */

namespace Petstore30;

/**
 * Class ApiResponse
 *
 * @package Petstore30
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @OAS\Schema(
 *     type="object",
 *     description="Api response",
 *     title="Api response"
 * )
 */
class ApiResponse
{
    /**
     * @OAS\Property(
     *     description="Code",
     *     title="Code",
     *     format="int32"
     * )
     *
     * @var int
     */
    private $code;

    /**
     * OAS\Property(
     *    description="Type",
     *    title="Type",
     * )
     *
     * @var string
     */
    private $type;

    /**
     * @OAS\Property(
     *     description="Message",
     *     title="Message"
     * )
     *
     * @var string
     */
    private $message;
}
