<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'TypeSchema')]
class TypeSchema
{
    #[OA\Property]
    public int $id;

    #[OA\Property]
    public string $name;

    #[OA\Property]
    public ?float $score = null;

    #[OA\Property]
    public bool $active;

    /** @var list<string> */
    #[OA\Property]
    public array $tags;
}
