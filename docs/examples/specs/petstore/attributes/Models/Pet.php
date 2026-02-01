<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes\Models;

use OpenApi\Attributes as OAT;

/**
 * Class Pet.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
#[OAT\Schema(title: 'Pet model', description: 'Pet model', required: [
    'name',
    'photoUrls',
], xml: new OAT\Xml(
    name: 'Pet'
))]
class Pet
{
    #[OAT\Property(title: 'ID', description: 'ID', format: 'int64')]
    private readonly int $id;

    #[OAT\Property(
        title: 'Category'
    )]
    private readonly Category $category;

    #[OAT\Property(title: 'Pet name', description: 'Pet name', format: 'int64')]
    private readonly string $name;

    #[OAT\Property(title: 'Photo urls', description: 'Photo urls', items: new OAT\Items(
        type: 'string',
        default: 'images/image-1.png'
    ), xml: new OAT\Xml(
        name: 'photoUrl',
        wrapped: true
    ))]
    private readonly array $photoUrls;

    #[OAT\Property(title: 'Pet tags', description: 'Pet tags', items: new OAT\Items(
        type: Tag::class
    ), xml: new OAT\Xml(
        name: 'tag',
        wrapped: true
    ))]
    /**
     * @var array<Tag>
     */
    private readonly array $tags;
}
