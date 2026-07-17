<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Spec\Models;

use OpenApi\Spec as OA;

/**
 * Pets Category.
 */
#[OA\Schema(
    title: 'Pets Category.',
    xml: new OA\Xml(name: 'Category'),
)]
class Category
{
    #[OA\Property(property: 'id')]
    #[OA\Schema(title: 'ID', description: 'ID', format: 'int64')]
    private int $id;

    #[OA\Property(property: 'name')]
    #[OA\Schema(title: 'Category name', description: 'Category name')]
    private string $name;
}
