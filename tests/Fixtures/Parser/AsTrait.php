<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Parser;

/**
 * @OA\Schema(schema="as")
 */
trait AsTrait
{
    use OtherTrait;
}
