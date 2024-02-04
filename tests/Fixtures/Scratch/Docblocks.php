<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

/** @OA\Schema  */
class DocblockSchema
{
    /**
     * @OA\Property
     * @var string The name
     */
    public $name;

    /**
     * @OA\Property
     * @var string The name (old)
     *
     * @deprecated
     */
    public $oldName;
}

#[OAT\Schema]
class DocblockSchemaChild extends DocblockSchema
{
    /** @var int The id */
    #[OAT\Property]
    public $id;
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
class DockblockEndpoint
{
}
