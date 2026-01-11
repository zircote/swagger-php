<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Processors;

use OpenApi\Attributes as OAT;

/**
 * Entity controller trait.
 */
trait EntityControllerTrait
{
    #[OAT\Delete(
        path: '/entities/{id}',
        tags: ['EntityController'],
    )]
    #[OAT\Response(response: 'default', description: 'successful operation')]
    public function deleteEntity($id)
    {
    }
}
