<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Processors;

use OpenApi\Attributes as OAT;

/**
 * An entity controller class.
 */
#[OAT\Info(version: '1.0.0')]
class EntityControllerClass implements EntityControllerInterface
{
    use EntityControllerTrait;

    #[OAT\Get(
        path: '/entities/{id}',
        tags: ['EntityController'],
    )]
    #[OAT\Response(response: 'default', description: 'successful operation')]
    public function getEntry($id)
    {
    }

    /**
     * @inheritDoc
     */
    public function updateEntity($id)
    {
    }
}
