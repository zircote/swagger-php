<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
final class CustomOpenApiPropertySpec extends OA\Property
{
}

#[OA\Info(title: 'Api', version: '1.0.0')]
#[OA\Operation\Get(
    path: '/api',
    operationId: 'api',
)]
#[OA\Response(response: 200, description: 'All good')]
class CustomOpenApiPropertyControllerSpec
{
}

#[OA\Schema(schema: 'CustomPropertyAttribute')]
class CustomPropertyAttributeSpec
{
    public function __construct(
        #[CustomOpenApiPropertySpec]
        public ?int $number,
    ) {
    }
}
