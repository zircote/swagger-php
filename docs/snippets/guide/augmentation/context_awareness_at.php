<?php

use OpenApi\Attributes as OA;

#[OA\Schema()]
class Product
{
    /**
     * The product name,.
     * @var string
     */
    #[OA\Property()]
    public $name;
}
