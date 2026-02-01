<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes\Models;

use OpenApi\Attributes as OAT;

/**
 * Tag.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
#[OAT\Schema(
    title: 'Tag',
    xml: new OAT\Xml(
        name: 'Tag'
    )
)]
class Tag
{
    #[OAT\Property(
        title: 'ID',
        description: 'ID',
        format: 'int64'
    )]
    private int $id;

    #[OAT\Property(
        title: 'Name',
        description: 'Name'
    )]
    private string $name;
}
