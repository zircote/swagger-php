<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;

/**
 * @deprecated since 7.0, removed in 8.0 - use `OpenApi\Spec\Operation\Delete` instead
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Delete extends OA\Delete
{
    use OperationTrait;
}
