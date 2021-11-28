<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Parser;

/**
 * @OA\Schema(schema="as")
 */
trait AsTrait
{
    use OtherTrait;
}
