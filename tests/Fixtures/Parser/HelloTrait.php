<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Parser;

use OpenApiTests\Fixtures\Parser\AsTrait as Aliased;

/**
 * @OA\Schema(schema="hello")
 */
trait HelloTrait
{
    use OtherTrait, Aliased;

    /**
     * @OA\Property()
     */
    public $greet = 'Hello!';
}
