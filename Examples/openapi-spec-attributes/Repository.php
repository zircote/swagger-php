<?php

namespace OpenApi\Examples\OpenapiSpecAttributes;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="repository")
 */
#[OA\Schema(schema: 'repository')]
class Repository
{

    /**
     * @OA\Property()
     * @var string
     */
    #[OA\Property(type: 'string')]
    public $slug;

    /**
     * @OA\Property()
     * @var User
     */
    #[OA\Property(ref: '#/components/schemas/user')]
    public $owner;
}
