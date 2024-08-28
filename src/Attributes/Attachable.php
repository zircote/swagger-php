<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;

#[\Attribute(\Attribute::TARGET_ALL | \Attribute::IS_REPEATABLE)]
class Attachable extends OA\Attachable
{
    public function __construct(array $properties = [])
    {
        parent::__construct($properties);
    }
}
