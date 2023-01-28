<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema
 */
interface BaseInterface
{
    /**
     * @OA\Property(property="interfaceProperty");
     *
     * @var string
     */
    public function getInterfaceProperty();
}
