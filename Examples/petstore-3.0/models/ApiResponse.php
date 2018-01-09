<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
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
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private $code;

    /**
     * OAS\Property(
     *    description="Type",
     *    title="Type",
     * )
     *
     * @var string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private $type;

    /**
     * @OAS\Property(
     *     description="Message",
     *     title="Message"
     * )
     *
     * @var string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private $message;
}