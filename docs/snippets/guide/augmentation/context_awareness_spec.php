<?php

namespace Openapi\Snippets\Augmentation\Aware;

use OpenApi\Spec as OA;

#[OA\Schema]
class Product
{
    /**
     * The product name,.
     * @var string
     */
    #[OA\Property]
    public $name;
}
