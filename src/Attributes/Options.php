<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Options extends \OpenApi\Annotations\Options
{
    use OperationTrait;
}
