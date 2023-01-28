<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Samples;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'bool',
    x: ['custom-tag' => false],
)]
#[OA\Tag(
    name: 'int',
    x: ['custom-tag' => 2],
)]
#[OA\Tag(
    name: 'string',
    x: ['custom-tag' => 'foo'],
)]
class VendorExtensions
{
}
