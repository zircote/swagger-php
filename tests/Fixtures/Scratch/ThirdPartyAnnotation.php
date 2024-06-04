<?php declare(strict_types=1);

// intentionally none: namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Annotations as OA;

/**
 * @Annotation
 */
class ThirdPartyAnnotation
{
}

/**
 * @OA\Schema
 * @ThirdPartyAnnotation
 */
class SomeParent
{
}

/**
 * @OA\Schema
 */
class Child extends SomeParent
{
}

/**
 * @OA\Info(title="API", version="1.0")
 * @OA\Get(
 *     path="/api/endpoint",
 *     @OA\Response(
 *         response=200,
 *         description="successful operation"
 *     )
 * )
 */
class ThirdPartyAnnotationEndpoint
{
}
