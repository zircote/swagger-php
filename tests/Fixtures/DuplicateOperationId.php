<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Duplicate operationId', version: 'unittest')]
class DuplicateOperationId
{
    #[OAT\Get(
        path: '/items/{item_name}',
        operationId: 'getItem',
        responses: [new OAT\Response(response: 'default', description: 'OK')]
    )]
    public function getItem()
    {
    }

    #[OAT\Get(
        path: '/admin/items/{item_name}',
        operationId: 'getItem',
        responses: [new OAT\Response(response: 'default', description: 'OK')]
    )]
    public function getAdminItem()
    {
    }
}
