<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ExtendedModel",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Base"),
 *     }
 * )
 */
class Extended extends Base
{
    /**
     * @OA\Property
     *
     * @var string
     */
    public $extendedProperty;
}
