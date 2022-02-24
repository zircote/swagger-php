<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

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
