<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema
 */
class PropertyInheritance extends AbstractBaseClass
{
    /** @OA\Property(property="inheritedfilter") */
    public string $filters;
}

/**
 * @OA\Info(title="API", version="1.0")
 * @OA\Get(
 *     path="/api/endpoint",
 *     @OA\Response(
 *         response=200,
 *         description="successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/PropertyInheritance")
 *     )
 * )
 */
class PropertyInheritanceEndpoint
{
}
