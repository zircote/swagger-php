<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

/**
 * A documented schema whose description is suppressed.
 */
#[OA\Schema(schema: 'SuppressedSchema', description: null)]
class SuppressedSchema
{
    #[OA\Property(property: 'name')]
    public string $name;
}
