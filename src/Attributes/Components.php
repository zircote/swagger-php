<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Components extends \OpenApi\Annotations\Components
{
    public function __construct(array $properties = [])
    {
        parent::__construct($properties);
    }
}
