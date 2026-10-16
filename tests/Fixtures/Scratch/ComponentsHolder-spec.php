<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Components holder', version: '1.0')]
#[OA\Operation\Post(
    path: '/things',
    operationId: 'postThing',
    parameters: [new OA\Parameter(ref: '#/components/parameters/dryRun')],
    requestBody: new OA\RequestBody(ref: '#/components/requestBodies/newThing'),
)]
#[OA\Response(response: 201, description: 'Created', content: new OA\MediaType\Json(schema: new OA\Schema(ref: '#/components/schemas/thing')))]
#[OA\Response(response: 404, ref: '#/components/responses/notFound')]
class ComponentsHolderEndpointSpec
{
}

// none of these carries a key: the wrapper makes each method a component named after it,
// except the parameter, which is keyed by its name wherever it is declared
#[OA\Components]
class ComponentsHolderSpec
{
    #[OA\Schema(type: OA\Type::String)]
    public $owner;

    #[OA\Schema(type: OA\Type::Object, properties: ['id' => OA\Type::Integer, 'owner' => new OA\Schema(ref: '#/components/schemas/owner')])]
    public function thing(): void
    {
    }

    #[OA\Response(description: 'Not found')]
    public function notFound(): void
    {
    }

    #[OA\RequestBody(content: new OA\MediaType\Json(schema: new OA\Schema(ref: '#/components/schemas/thing')))]
    public function newThing(): void
    {
    }

    #[OA\Parameter\Query(name: 'dryRun', schema: new OA\Schema(type: OA\Type::Boolean))]
    public function dryRunParameter(): void
    {
    }
}
