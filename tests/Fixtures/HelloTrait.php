<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures;

/**
 * @OA\Schema(schema="trait")
 */
trait HelloTrait
{

    /**
     * @OA\Property()
     */
    public $greet = 'Hello!';
}
