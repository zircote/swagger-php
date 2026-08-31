<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Link', version: '1.0')]
class LinkController
{
    #[OAT\Get(
        path: '/widgets/{id}',
        operationId: 'getWidget',
        responses: [
            new OAT\Response(
                response: 200,
                description: 'The widget',
                links: [
                    new OAT\Link(link: 'widgetOwner', ref: '#/components/links/WidgetOwner'),
                ],
            ),
        ],
    )]
    #[OAT\PathParameter(name: 'id', schema: new OAT\Schema(type: 'string'))]
    public function getWidget()
    {
    }

    #[OAT\Get(
        path: '/owners/{id}',
        operationId: 'getOwner',
        responses: [
            new OAT\Response(response: 200, description: 'The owner'),
        ],
    )]
    #[OAT\PathParameter(name: 'id', schema: new OAT\Schema(type: 'string'))]
    #[OAT\Link(link: 'WidgetOwner', operationId: 'getOwner', parameters: ['id' => '$response.body#/ownerId'])]
    public function getOwner()
    {
    }
}
