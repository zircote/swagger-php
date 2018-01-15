<?php

namespace PetstoreIO;

/**
 * @OAS\Schema(type="object")
 */
class ApiResponse
{

    /**
     * @OAS\Property(format="int32")
     * @var int
     */
    public $code;

    /**
     * @OAS\Property
     * @var string
     */
    public $type;

    /**
     * @OAS\Property
     * @var string
     */
    public $message;
}
