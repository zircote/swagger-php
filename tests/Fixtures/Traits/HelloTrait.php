<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Traits;

/**
 * @OA\Schema(schema="trait")
 */
trait HelloTrait
{
    use OtherTrait;

    /**
     * @OA\Property()
     */
    public $greet = 'Hello!';
}
