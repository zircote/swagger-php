<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ComponentKey;

use OpenApi\Spec as OA;

// Every component keyed the historic way — the field named after its type. The document this
// produces is the one `ComponentSpelling` produces, which is what makes the alias exact.

#[OA\Schema(schema: 'Pet', type: 'object')]
class LegacyPet
{
}

#[OA\Response(response: 'NotFound', description: 'No such pet')]
class LegacyNotFound
{
}

#[OA\RequestBody(request: 'PetBody', description: 'A pet', content: [new OA\MediaType\Json(ref: '#/components/schemas/Pet')])]
class LegacyPetBody
{
}

#[OA\Components]
#[OA\Parameter(parameter: 'page', name: 'page', in: 'query', schema: new OA\Schema(type: 'integer'))]
#[OA\Header(header: 'RateLimit', description: 'Requests left', schema: new OA\Schema(type: 'integer'))]
#[OA\Link(link: 'Self', operationId: 'getPet')]
class LegacyShared
{
}

// An example beside a parameter and a header is an ambiguous merge — both take examples — so
// it gets a wrapper of its own.
#[OA\Components]
#[OA\Example(example: 'Minimal', value: ['name' => 'Rex'])]
class LegacyMinimalExample
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
class LegacyController
{
}
