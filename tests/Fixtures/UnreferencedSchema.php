<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

/**
 * One operation and one schema nothing references, which spec's `Cleanup` prunes by default.
 */
#[OAT\Info(title: 'Unreferenced schema', version: 'unittest')]
class UnreferencedSchema
{
    #[OAT\Get(path: '/items', responses: [new OAT\Response(response: 200, description: 'OK')])]
    public function getItems()
    {
    }
}

#[OAT\Schema]
class UnreferencedSchemaModel
{
    #[OAT\Property]
    public string $name;
}
