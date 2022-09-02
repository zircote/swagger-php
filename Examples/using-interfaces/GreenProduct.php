<?php

namespace OpenApi\Examples\UsingInterfaces;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(title="GreenProduct")
 */
class GreenProduct extends Product implements ColorInterface
{
    /**
     * @inheritDoc
     */
    public function getColor()
    {
        return 'green';
    }
}
