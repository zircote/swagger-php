<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Tags', version: '1.0')]
#[OA\Tag(name: 'users', summary: 'Users')]
#[OA\Tag(name: 'admin', description: 'Needs an admin')]
class TagHolder
{
}
