<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ContentSchema;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Content schema', version: '1.0')]
class PetController
{
    #[OA\Operation\Post(path: '/pets', operationId: 'addPet', requestBody: new OA\RequestBody(content: new OA\Schema\Ref(Pet::class)))]
    #[OA\Response(response: 200, description: 'The pet', content: new OA\Schema(ref: Pet::class))]
    #[OA\Response(response: 400, description: 'Invalid', content: new OA\Schema(type: 'object', properties: ['message' => new OA\Schema(type: 'string')]))]
    public function add(): void
    {
    }

    #[OA\Operation\Get(path: '/pets', operationId: 'listPets')]
    #[OA\Response(response: 200, description: 'The pets', content: [
        new OA\MediaType(schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: Pet::class))),
        new OA\MediaType\Xml(schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: Pet::class))),
    ])]
    public function list(): void
    {
    }
}
