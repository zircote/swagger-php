<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Spec\Models;

use OpenApi\Spec as OA;

/**
 * Tag.
 */
#[OA\Schema(
    title: 'Tag',
    xml: new OA\Xml(name: 'Tag'),
)]
class Tag
{
    #[OA\Property(property: 'id')]
    #[OA\Schema(title: 'ID', description: 'ID', format: 'int64')]
    private int $id;

    #[OA\Property(property: 'name')]
    #[OA\Schema(title: 'Name', description: 'Name')]
    private string $name;
}
