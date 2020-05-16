<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Parser;

/**
 * @OA\Schema(schema="hello_trait")
 */
trait HelloTrait
{
    use OtherTrait;

    /**
     * @OA\Property()
     */
    public $greet = 'Hello!';
}
