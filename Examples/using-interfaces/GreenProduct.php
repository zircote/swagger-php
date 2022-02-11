<?php

namespace OpenApi\Examples\UsingInterfaces;

/**
 * @OA\Schema(title="Pet")
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
