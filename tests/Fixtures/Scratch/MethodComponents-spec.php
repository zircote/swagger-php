<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Method components', version: '1.0')]
#[OA\Operation\Get(path: '/things', operationId: 'getThings', parameters: [new OA\Parameter\Query(ref: '#/components/parameters/page')])]
#[OA\Response(response: 200, description: 'OK', content: new OA\MediaType\Json(schema: new OA\Schema(ref: '#/components/schemas/Thing')))]
#[OA\Response(response: 404, ref: '#/components/responses/notFound')]
class MethodComponentsEndpointSpec
{
}

#[OA\Operation\Post(path: '/things', operationId: 'postThing', requestBody: new OA\RequestBody(ref: '#/components/requestBodies/newThing'))]
#[OA\Response(response: 201, description: 'Created', content: new OA\MediaType\Json(schema: new OA\Schema(ref: '#/components/schemas/Other')))]
class MethodComponentsPostSpec
{
}

// components declared on methods that exist only to carry them, rather than on the class
class MethodComponentsHolderSpec
{
    #[OA\Schema(component: 'Thing', type: OA\Type::Object, properties: ['id' => OA\Type::Integer])]
    public function thing(): void
    {
    }

    #[OA\Schema(component: 'Other', type: OA\Type::Object, properties: ['name' => OA\Type::String])]
    public function model(): void
    {
    }

    #[OA\Parameter\Query(component: 'page', name: 'page', schema: new OA\Schema(type: OA\Type::Integer))]
    #[OA\Response(component: 'notFound', description: 'Not found')]
    #[OA\RequestBody(component: 'newThing', content: new OA\MediaType\Json(schema: new OA\Schema(ref: '#/components/schemas/Other')))]
    public function reusable(): void
    {
    }
}
