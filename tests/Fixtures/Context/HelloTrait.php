<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Context;

/**
 * @OA\Schema(schema="context_trait")
 */
trait HelloTrait
{
    use OtherTrait;

    /**
     * @OA\Property()
     */
    public $greet = 'Hello!';
}
