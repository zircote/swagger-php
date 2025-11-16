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
#[OAT\Schema(
    description: 'Pet model',
    title: 'Pet model',
    required: [
        'name',
        'photoUrls',
    ],
    xml: new OAT\Xml(
        name: 'Pet'
    )
)]
class Pet
{
    /**
     * The description.
     */
    #[OAT\Property(
        description: '',
        title: 'ID',
        format: 'int64'
    )]
    private int $id;

    #[OAT\Property(
        title: 'Category'
    )]
    private Category $category;

    #[OAT\Property(
        description: 'Pet name',
        title: 'Pet name',
        format: 'int64'
    )]
    private string $name;

    #[OAT\Property(
        description: 'Photo urls',
        title: 'Photo urls',
        xml: new OAT\Xml(
            name: 'photoUrl',
            wrapped: true
        ),
        items: new OAT\Items(
            type: 'string',
            default: 'images/image-1.png'
        )
    )]
    private array $photoUrls;

    #[OAT\Property(
        description: 'Pet tags',
        title: 'Pet tags',
        xml: new OAT\Xml(
            name: 'tag',
            wrapped: true
        ),
        items: new OAT\Items(
            type: Tag::class
        )
    )]
    /**
     * @var array<Tag>
     */
    private array $tags;
}
