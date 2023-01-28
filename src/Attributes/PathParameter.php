<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class PathParameter extends Parameter
{
    /**
     * @inheritdoc
     */
    public $in = 'path';

    /**
     * @inheritdoc
     */
    public $required = true;
}
