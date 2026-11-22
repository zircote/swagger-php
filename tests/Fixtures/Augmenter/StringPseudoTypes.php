<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'StringPseudoTypes')]
class StringPseudoTypes
{
    /** @var non-empty-string */
    #[OA\Property]
    public string $name;

    /** @var non-falsy-string|null */
    #[OA\Property]
    public ?string $code;

    /** @var numeric-string */
    #[OA\Property]
    public string $amount;

    /** @var list<non-empty-string> */
    #[OA\Property]
    public array $tags;

    /** @var non-empty-string|int */
    #[OA\Property]
    public string|int $key;

    /** @var non-empty-string */
    #[OA\Property(schema: new OA\Schema(minLength: 0))]
    public string $note;
}
