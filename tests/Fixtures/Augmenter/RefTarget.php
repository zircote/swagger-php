<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'RefTarget', description: 'A target for refs.')]
class RefTarget
{
    #[OA\Property(property: 'id')]
    public int $id;
}
