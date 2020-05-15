<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\StaticAnalyser;

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
