<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

/**
 * A documented schema.
 *
 * @deprecated
 */
#[OA\Schema(schema: 'DocblockSchema')]
class DocblockSchema
{
    #[OA\Property(property: 'name')]
    public string $name;
}
