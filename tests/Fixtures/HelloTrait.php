<?php declare(strict_types=1);

namespace OpenApiFixures;

/**
 * @OA\Schema
 */
trait Hello
{

    /**
     * @OA\Property()
     */
    public $greet = 'Hello!';
}
