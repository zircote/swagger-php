<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;

/**
 * @since OpenAPI 3.2.0
 *
 * @deprecated since 7.0, removed in 8.0 - use `OpenApi\Spec\Operation` with `method: 'query'` instead
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Query extends OA\Query
{
    use OperationTrait;
}
