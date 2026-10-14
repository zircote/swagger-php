<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

// an operation declared on the constructor, beside one on an ordinary method; spec ignores the
// constructor, so ConstructorOperation-spec.php declares it on the class
#[OAT\Info(title: 'Constructor operation', version: '1.0')]
class ConstructorOperationController
{
    #[OAT\Post(
        path: '/constructor',
        operationId: 'onConstructor',
        responses: [new OAT\Response(response: 201, description: 'Created')],
    )]
    public function __construct()
    {
    }

    #[OAT\Get(
        path: '/method',
        operationId: 'onMethod',
        responses: [new OAT\Response(response: 200, description: 'OK')],
    )]
    public function list(): void
    {
    }
}
