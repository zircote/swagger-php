<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\HybridBridge;

use OpenApi\Attributes as OAT;

#[OAT\Info(version: '1.0.0', title: 'Nested Schemas')]
class NestedSchemas
{
    /**
     * Describes the operation, not its schemas.
     *
     * @deprecated
     */
    #[OAT\Get(path: '/nested-schemas', responses: [new OAT\Response(
        response: 200,
        description: 'ok',
        headers: [
            new OAT\Header(header: 'X-Unset', schema: new OAT\Schema(type: 'integer')),
            new OAT\Header(header: 'X-Suppressed', description: null, schema: new OAT\Schema(type: 'integer')),
        ],
        content: new OAT\JsonContent(type: 'array', items: new OAT\Items(
            type: 'object',
            properties: [new OAT\Property(property: 'name', type: 'string')],
        )),
    )])]
    public function get(): void
    {
    }
}
