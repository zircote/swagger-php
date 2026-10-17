<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Tags', version: '1.0')]
#[OAT\Tag(name: 'users', description: 'Everything about users')]
#[OAT\Tag(name: 'list', description: 'Listings')]
class ClassicTaggedController
{
    #[OAT\Tag(name: 'list')]
    #[OAT\Get(path: '/users', tags: ['list'])]
    #[OAT\Response(response: 200, description: 'OK')]
    public function list(): void
    {
    }
}
