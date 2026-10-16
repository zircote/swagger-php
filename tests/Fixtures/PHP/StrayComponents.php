<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Stray components', version: '1.0')]
#[OA\Operation\Get(path: '/things', responses: [
    new OA\Response(response: 200, description: 'OK', content: new OA\MediaType\Json(schema: new OA\Schema(oneOf: [
        new OA\Schema(ref: '#/components/schemas/onMethod'),
        new OA\Schema(ref: '#/components/schemas/onProperty'),
    ]))),
])]
class StrayComponentsEndpoint
{
}

// keyed, but the class carries no `#[OA\Components]`
class StrayComponents
{
    #[OA\Schema(component: 'onProperty', type: OA\Type::Object)]
    public $property;

    #[OA\Schema(component: 'onMethod', type: OA\Type::Object)]
    public function method(): void
    {
    }
}
