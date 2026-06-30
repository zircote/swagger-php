<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(
    title: 'Required From Properties Scratch',
    version: '1.0'
)]
#[OAT\Schema]
class RequiredFromProperties
{
    #[OAT\Property(required: true)]
    public string $id;

    #[OAT\Property(required: false)]
    public string $nickname;

    #[OAT\Property]
    public string $note;
}

class RequiredFromPropertiesController
{
    #[OAT\Get(
        path: '/items',
        operationId: 'getItems',
        responses: [
            new OAT\Response(
                response: 200,
                description: 'OK',
                content: new OAT\JsonContent(ref: RequiredFromProperties::class),
            ),
        ],
    )]
    public function getItems()
    {
    }
}
