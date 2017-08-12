<?php declare(strict_types=1);
namespace SwaggerFixures;

/**
 * @SWG\Schema
 */
trait Hello
{

    /**
     * @SWG\Property()
     */
    public $greet = 'Hello!';
}
