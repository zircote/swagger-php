<?php

namespace OpenApi\Examples\UsingInterfaces;

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
