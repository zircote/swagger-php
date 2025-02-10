<?php declare(strict_types=1);

namespace OpenApi\Examples\Specs\Webhooks\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(required={"id", "name"})
 */
final class Pet
{
    /**
     * @OA\Property(format="int64")
     *
     * @var int
     */
    public $id;

    /**
     * @OA\Property
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property
     *
     * @var string
     */
    public $tag;
}
