<?php

namespace OpenApi\Tests\Fixtures\ExpandClasses;

/**
 * @OA\Schema()
 */
interface BaseInterface
{

    /**
     * @OA\Property(property="interfaceProperty");
     * @var string
     */
    public function getInterfaceProperty();
}
