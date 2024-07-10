<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

#[OAT\Schema]
class PromotedPropertyDescription
{
    /**
     * Property name.
     */
    #[OAT\Property(property: 'thename')]
    public string $name = '';

    public function __construct(
        /**
         * Property value.
         *
         * @var string
         */
        #[OAT\Property(property: 'thevalue')]
        public string $value = '',

        /**
         * Other value.
         *
         * @var string
         */
        #[OAT\Property(property: 'other')]
        public string $other = '',

        /**
         * Property meta.
         *
         * @var string
         *
         * @OA\Property(property="themeta")
         */
        public string $meta = '',
    ) {
    }
}

#[OAT\Info(
    title: 'Promoted Property Description Scratch',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class PromotedPropertyDescriptionEndpoint
{
}
