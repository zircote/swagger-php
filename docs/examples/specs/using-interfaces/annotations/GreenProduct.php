<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingInterfaces\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(title="GreenProduct")
 */
class GreenProduct extends Product implements ColorInterface
{
    /**
     * @inheritDoc
     */
    public function getColor(): string
    {
        return 'green';
    }
}
