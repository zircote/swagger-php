<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

#[\Attribute(\Attribute::TARGET_ALL | \Attribute::IS_REPEATABLE)]
class Attachable extends \OpenApi\Annotations\Attachable
{
    public function __construct(array $properties = [])
    {
        parent::__construct($properties);
    }
}
