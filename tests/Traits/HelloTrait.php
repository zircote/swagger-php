<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Traits;

use OpenApi\Annotations as OA;

trait Hello
{

    /**
     * @OA\Property(
     *     property="greet",
     *     description="description"
     * )
     */
    public $greet = 'Hello!';
}
