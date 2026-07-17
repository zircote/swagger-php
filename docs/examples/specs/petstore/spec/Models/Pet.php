<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Spec\Models;

use OpenApi\Spec as OA;

/**
 * Class Pet.
 */
#[OA\Schema(title: 'Pet model', description: 'Pet model', required: [
    'name',
    'photoUrls',
], xml: new OA\Xml(name: 'Pet'))]
class Pet
{
    #[OA\Property(property: 'id')]
    #[OA\Schema(title: 'ID', description: 'ID', format: 'int64')]
    private int $id;

    #[OA\Property(property: 'category')]
    #[OA\Schema(title: 'Category', ref: Category::class)]
    private Category $category;

    #[OA\Property(property: 'name')]
    #[OA\Schema(title: 'Pet name', description: 'Pet name', format: 'int64')]
    private string $name;

    /**
     * @var array<string>
     */
    #[OA\Property(property: 'photoUrls')]
    #[OA\Schema(
        title: 'Photo urls',
        description: 'Photo urls',
        type: 'array',
        items: new OA\Schema(type: 'string', default: 'images/image-1.png'),
        xml: new OA\Xml(name: 'photoUrl', wrapped: true),
    )]
    private array $photoUrls;

    /**
     * @var array<Tag>
     */
    #[OA\Property(property: 'tags')]
    #[OA\Schema(
        title: 'Pet tags',
        description: 'Pet tags',
        type: 'array',
        items: new OA\Schema(ref: Tag::class),
        xml: new OA\Xml(name: 'tag', wrapped: true),
    )]
    private array $tags;
}
