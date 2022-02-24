<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

/**
 * @OA\Schema
 */
class Php8AttrMix
{
    #[Label('Id', [1])]
    /** @OA\Property */
    public string $id = '';

    /** @OA\Property() */
    #[
        Label('OtherId', [2, 3]),
        Label('OtherId', [2, 3]),
    ]
    public string $otherId = '';
}
