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
    #[OA\Schema(title: 'ID', description: 'ID', format: OA\Format::Int64)]
    private int $id;

    #[OA\Property]
    #[OA\Schema(title: 'Category', ref: Category::class)]
    private Category $category;

    #[OA\Property]
    #[OA\Schema(title: 'Pet name', description: 'Pet name', format: 'int64')]
    private string $name;

    /**
     * @var array<string>
     */
    #[OA\Property]
    #[OA\Schema(
        title: 'Photo urls',
        description: 'Photo urls',
        items: new OA\Schema(type: 'string', default: 'images/image-1.png'),
        xml: new OA\Xml(name: 'photoUrl', wrapped: true),
    )]
    private array $photoUrls;

    /**
     * @var array<Tag>
     */
    #[OA\Schema\Items(
        title: 'Pet tags',
        description: 'Pet tags',
        ref: Tag::class,
        xml: new OA\Xml(name: 'tag', wrapped: true),
    )]
    private array $tags;
}
