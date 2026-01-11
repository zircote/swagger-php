<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Processors;

use OpenApi\Attributes as OAT;

/**
 * Entity controller interface.
 */
interface EntityControllerInterface
{
    #[OAT\Post(
        path: '/entities/{id}',
        tags: ['EntityController'],
    )]
    #[OAT\Response(response: 'default', description: 'successful operation')]
    public function updateEntity($id);
}
