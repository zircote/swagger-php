<?php
/**
 * @OA\Schema(required={"code", "message"}, type="object")
 */
class ErrorModel extends Exception
{
    /**
     * @OA\Property(format="int32");
     * @var int
     */
    public $code;
    /**
     * @OA\Property();
     * @var string
     */
    public $message;
}
