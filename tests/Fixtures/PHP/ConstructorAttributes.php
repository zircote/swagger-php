<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Constructor attributes', version: '1.0')]
class ConstructorAttributesController
{
    #[OA\Operation\Post(path: '/constructor', operationId: 'onConstructor')]
    #[OA\Response(response: 201, description: 'Created')]
    public function __construct()
    {
    }

    #[OA\Operation\Get(path: '/method', operationId: 'onMethod')]
    #[OA\Response(response: 200, description: 'OK')]
    public function list(): void
    {
    }
}

// inherits the constructor, which is reported once, where it is declared
class ConstructorAttributesChild extends ConstructorAttributesController
{
}
