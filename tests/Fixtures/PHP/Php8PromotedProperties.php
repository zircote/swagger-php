<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Attributes as OAT;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema
 */
class Php8PromotedProperties
{
    public function __construct(
        /**
         * Label List.
         *
         * @var Label[]|null $labels
         *
         * @OA\Property()
         */
        public ?array $labels,
        #[OAT\Property()]
        public int $id,
    ) {
    }
}
