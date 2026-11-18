<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

/**
 * Methods without a field of their own in the path item, which OpenAPI 3.2 writes to
 * `additionalOperations`.
 *
 * The 3.0 and 3.1 documents are the assertion that matters as much as the 3.2 one: they keep the
 * `get` and leave out every other method, with a warning each.
 */
#[OA\Info(title: 'Additional operations', version: '1.0')]
class AdditionalOperationsSpec
{
    #[OA\Operation\Get(path: '/files/{name}', operationId: 'getFile')]
    #[OA\Parameter\Path(name: 'name', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'The file')]
    public function show()
    {
    }

    #[OA\Operation(path: '/files/{name}', method: 'COPY', operationId: 'copyFile')]
    #[OA\Parameter\Path(name: 'name', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter\Header(name: 'Destination', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 201, description: 'Copied')]
    public function copy()
    {
    }

    #[OA\Operation(path: '/files/{name}', method: 'lock', operationId: 'lockFile')]
    #[OA\Parameter\Path(name: 'name', schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(content: new OA\MediaType(mediaType: 'application/xml', schema: new OA\Schema(type: 'string')))]
    #[OA\Response(response: 200, description: 'Locked')]
    public function lock()
    {
    }
}
