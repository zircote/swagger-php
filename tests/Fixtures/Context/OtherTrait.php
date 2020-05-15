<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Context;

/**
 * @OA\Schema(schema="other")
 */
trait OtherTrait
{

    /**
     * @OA\Property()
     */
    public $so = 'what?';
}
