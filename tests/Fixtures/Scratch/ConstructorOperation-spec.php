<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

// spec reads nothing on the constructor, so the operation classic takes from there is declared
// on the class, beside the one on an ordinary method
#[OA\Info(title: 'Constructor operation', version: '1.0')]
#[OA\Operation(path: '/constructor', method: 'post', operationId: 'onConstructor')]
#[OA\Response(response: 201, description: 'Created')]
class ConstructorOperationControllerSpec
{
    #[OA\Operation\Get(path: '/method', operationId: 'onMethod')]
    #[OA\Response(response: 200, description: 'OK')]
    public function list(): void
    {
    }
}
