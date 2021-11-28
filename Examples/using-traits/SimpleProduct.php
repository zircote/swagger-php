<?php

namespace OpenApi\Examples\UsingTraits;

/**
 * @OA\Schema(title="SimpleProduct model")
 * )
 */
class SimpleProduct
{
    use Decoration\Bells;
    use Decoration\UndocumentedBell;

    /**
     * The unique identifier of a simple product in our catalog.
     *
     * @var integer
     * @OA\Property(format="int64", example=1)
     */
    public $id;
}
