<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Parser;

/**
 * @OA\Schema(schema="other_trait")
 */
trait OtherTrait
{

    /**
     * @OA\Property()
     */
    public $so = 'what?';
}
