<?php

namespace Openapi\Snippets\Cookbook\ClassConstants;

use OpenApi\Spec as OA;

#[OA\Schema]
class Airport
{
    #[OA\Property(property: 'kind')]
    public const KIND = 'Airport';
}
