<?php declare(strict_types=1);

namespace SwaggerFixures;

/**
 * @OAS\Schema
 */
trait Hello
{

    /**
     * @OAS\Property()
     */
    public $greet = 'Hello!';
}
