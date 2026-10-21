<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;

/**
 * @deprecated since 7.0, removed in 8.0 - use `OpenApi\Spec\Attachable` instead
 */
#[\Attribute(\Attribute::TARGET_ALL | \Attribute::IS_REPEATABLE)]
class Attachable extends OA\Attachable
{
    public function __construct(array $properties = [])
    {
        parent::__construct($properties);
    }
}
