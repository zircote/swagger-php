<?php

namespace Openapi\Snippets\Augmentation\Aware;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema
 */
class Product
{
    /**
     * The product name,.
     * @var string
     * @OA\Property
     */
    public $name;
}
