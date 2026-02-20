<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

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
        /**
         * Tag List.
         *
         * @var array<int,string>
         */
        #[OAT\Property]
        public array $tags,
        #[OAT\Property]
        public int $id,
    ) {
    }
}
