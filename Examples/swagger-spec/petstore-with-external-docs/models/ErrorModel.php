<?php
/**
 * @OAS\Schema(required={"code", "message"}, type="object")
 */
class ErrorModel extends Exception
{
    /**
     * @OAS\Property(format="int32");
     * @var int
     */
    public $code;
    /**
     * @OAS\Property();
     * @var string
     */
    public $message;
}
