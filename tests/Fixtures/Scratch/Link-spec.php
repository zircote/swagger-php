<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Link', version: '1.0')]
class LinkControllerSpec
{
    #[OA\Operation\Get(
        path: '/widgets/{id}',
        operationId: 'getWidget',
        responses: [
            new OA\Response(
                response: 200,
                description: 'The widget',
                links: [
                    new OA\Link(link: 'widgetOwner', ref: '#/components/links/WidgetOwner'),
                ],
            ),
        ],
    )]
    public function getWidget(
        #[OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'string'))]
        string $id
    ) {
    }

    #[OA\Operation\Get(
        path: '/owners/{id}',
        operationId: 'getOwner',
        responses: [
            new OA\Response(response: 200, description: 'The owner'),
        ],
    )]
    #[OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'string'))]
    #[OA\Link(link: 'WidgetOwner', operationId: 'getOwner', parameters: ['id' => '$response.body#/ownerId'])]
    public function getOwner()
    {
    }
}
