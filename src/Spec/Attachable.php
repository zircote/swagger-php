<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Base class for custom attributes.
 *
 * By default not allowed to contain other attributes, but can be inline nested into any other attribute
 * (including itself).
 */
#[\Attribute(\Attribute::TARGET_ALL | \Attribute::IS_REPEATABLE)]
class Attachable extends AbstractAttribute
{
    public function isRoot(): bool
    {
        return true;
    }
}
