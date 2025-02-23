<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes\Models;

use OpenApi\Attributes as OAT;

/**
 * Pets Category.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
#[OAT\Schema(
    title: 'Pets Category.',
    xml: new OAT\Xml(
        name: 'Category'
    )
)]
class Category
{
    #[OAT\Property(
        description: 'ID',
        title: 'ID',
        format: 'int64'
    )]
    private int $id;

    #[OAT\Property(
        description: 'Category name',
        title: 'Category name'
    )]
    private string $name;
}
