<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\UnresolvedRefs;

use OpenApi\Attributes as OAT;

/**
 * References a class nothing describes, from inside a response, where the reference has no
 * location of its own.
 */
#[OAT\Info(version: '1.0.0', title: 'Unresolved references')]
class StationController
{
    #[OAT\Get(path: '/stations', responses: [new OAT\Response(
        response: 200,
        description: 'ok',
        content: new OAT\JsonContent(properties: [
            new OAT\Property(property: 'data', type: 'array', items: new OAT\Items(ref: Station::class)),
        ]),
    )])]
    public function index(): void
    {
    }
}
