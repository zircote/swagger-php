<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

class Timestamp extends \DateTimeImmutable
{
}

#[OA\Schema(component: 'DateTimeSubclass')]
class DateTimeSubclass
{
    #[OA\Property]
    public Timestamp $created;

    #[OA\Property]
    public ?Timestamp $deleted;

    /** @var Timestamp */
    #[OA\Property]
    public $updated;

    /** @var list<Timestamp> */
    #[OA\Property]
    public array $history;
}
