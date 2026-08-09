<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'MyEnum', type: 'string')]
enum MyEnumSpec: string
{
    case AA = 'AA';
}

#[OA\Schema(schema: 'PromotedPropertyDescription')]
class PromotedPropertyDescriptionSpec
{
    /**
     * Property name.
     */
    #[OA\Property(property: 'thename')]
    public string $name = '';

    public function __construct(
        /**
         * Property value.
         *
         * @var string
         */
        #[OA\Property(property: 'thevalue')]
        public string $value = '',

        /**
         * Other value.
         *
         * @var string
         */
        #[OA\Property(property: 'other')]
        public string $other = '',

        /**
         * Property meta.
         *
         * @var string
         */
        #[OA\Property(property: 'themeta')]
        public string $meta = '',

        /**
         * Property different.
         *
         * @var string
         */
        #[OA\Property]
        public string $different = '',

        /*
         * Intentionally not promoted!
         */
        #[OA\Property]
        MyEnumSpec $myEnum = MyEnumSpec::AA,
    ) {
    }
}

#[OA\Info(
    title: 'Promoted Property Description Scratch',
    version: '1.0'
)]
#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'getPromotedPropertyDescription',
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
class PromotedPropertyDescriptionEndpointSpec
{
}
