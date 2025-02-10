<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Nesting\Annotations;

use OpenApi\Annotations as OA;

/**
 * An entity controller class.
 *
 * @OA\Info(
 *     version="1.0.0",
 *     title="Nested schemas",
 *     description="Example info",
 *     @OA\Contact(name="Swagger API Team")
 * )
 * @OA\Server(
 *     url="https://example.localhost",
 *     description="API server"
 * )
 * @OA\Tag(
 *     name="api",
 *     description="All API endpoints"
 * )
 */
class ApiController
{
    /**
     * @OA\Get(
     *     tags={"api"},
     *     path="/entity/{id}",
     *     description="Get the entity",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ActualModel")
     *     )
     * )
     */
    public function get($id)
    {
    }
}
