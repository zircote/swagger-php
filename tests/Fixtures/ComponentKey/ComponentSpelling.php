<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ComponentKey;

use OpenApi\Spec as OA;

// Every component keyed by `component:`. See `LegacySpelling` for the same document spelled the
// historic way; the two producing one document is what makes the alias exact.

#[OA\Schema(component: 'Pet', type: 'object')]
class ComponentPet
{
}

#[OA\Response(component: 'NotFound', description: 'No such pet')]
class ComponentNotFound
{
}

#[OA\RequestBody(component: 'PetBody', description: 'A pet', content: [new OA\MediaType\Json(ref: '#/components/schemas/Pet')])]
class ComponentPetBody
{
}

#[OA\Components]
#[OA\Parameter(component: 'page', name: 'page', in: 'query', schema: new OA\Schema(type: 'integer'))]
#[OA\Header(component: 'RateLimit', description: 'Requests left', schema: new OA\Schema(type: 'integer'))]
#[OA\Link(component: 'Self', operationId: 'getPet')]
class ComponentShared
{
}

// An example beside a parameter and a header is an ambiguous merge — both take examples — so
// it gets a wrapper of its own.
#[OA\Components]
#[OA\Example(component: 'Minimal', value: ['name' => 'Rex'])]
class ComponentMinimalExample
{
}

#[OA\Info(title: 'ComponentKey', version: '1.0')]
#[OA\Operation\Post(
    path: '/pets/{id}',
    operationId: 'getPet',
    parameters: [new OA\Parameter(ref: '#/components/parameters/page')],
    requestBody: new OA\RequestBody(ref: '#/components/requestBodies/PetBody'),
    responses: [
        new OA\Response(
            response: 200,
            description: 'A pet',
            headers: [new OA\Header(header: 'X-Rate-Limit', ref: '#/components/headers/RateLimit')],
            content: [new OA\MediaType\Json(ref: '#/components/schemas/Pet', examples: [new OA\Example(example: 'minimal', ref: '#/components/examples/Minimal')])],
            links: [new OA\Link(link: 'self', ref: '#/components/links/Self')],
        ),
        new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
    ],
)]
class ComponentController
{
}
