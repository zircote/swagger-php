<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

final class Money
{
}

interface Labelled
{
}

#[OA\Schema(component: 'UndescribedClasses')]
class UndescribedClasses
{
    #[OA\Property]
    public Money $price;

    #[OA\Property]
    public Labelled $label;

    /** @var list<Money> */
    #[OA\Property]
    public array $history;

    #[OA\Property]
    public BackedStringEnum $status;

    #[OA\Property]
    public RefTarget $target;
}
