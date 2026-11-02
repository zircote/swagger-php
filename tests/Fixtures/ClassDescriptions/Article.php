<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ClassDescriptions;

use OpenApi\Spec as OA;

#[OA\Schema]
class Article
{
    #[OA\Property]
    public int $id;

    #[OA\Property]
    public Author $author;

    #[OA\Property(schema: new OA\Schema\Ref(ref: Author::class, qualifiers: ['groups' => ['light']]))]
    public Author $editor;
}
