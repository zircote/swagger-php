<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Traits;

/**
 * @OA\Schema(schema="stale")
 */
trait StaleTrait
{

    /**
     * @OA\Property()
     */
    public $expired = true;
}
