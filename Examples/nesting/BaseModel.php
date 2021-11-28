<?php declare(strict_types=1);

namespace OpenApi\Examples\Nesting;

/**
 * @OA\Schema()
 */
class BaseModel
{
    /**
     * @OA\Property()
     *
     * @var string
     */
    public $base;

}