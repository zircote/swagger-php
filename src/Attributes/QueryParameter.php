<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

/**
 * @deprecated since 7.0, removed in 8.0 - use `OpenApi\Spec\Parameter\Query` instead
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class QueryParameter extends Parameter
{
    /**
     * @inheritdoc
     */
    public $in = 'query';
}
