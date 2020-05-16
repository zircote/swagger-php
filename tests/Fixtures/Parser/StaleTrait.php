<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Parser;

/**
 * @OA\Schema(schema="stale_trait")
 */
trait StaleTrait
{

    /**
     * @OA\Property()
     */
    public $expired = true;
}
