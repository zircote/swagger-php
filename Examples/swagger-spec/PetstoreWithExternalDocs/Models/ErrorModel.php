<?php

namespace PetstoreWithExternalDocs\Models;

use Exception;
use Swagger\Annotations as SWG;

/**
 * @SWG\Definition(required={"code", "message"});
 */
class ErrorModel extends Exception
{
    /**
     * @SWG\Property(format="int32");
     * @var int
     */
    public $code;
    /**
     * @SWG\Property();
     * @var string
     */
    public $message;
}
