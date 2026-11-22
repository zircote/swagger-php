<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

/**
 * An object schema's `required` list, from the PHP declarations and from each property's own
 * `required`.
 *
 * `RequiredPropertiesInferred` is the rule alone: a property is required when its PHP type is not
 * nullable and it has no default. `RequiredPropertiesDeclared` overrides it both ways, and on a
 * getter, where nothing is inferred. The response's inline properties have no PHP member at all.
 * None of the flags may appear in a property's own schema.
 */
#[OA\Info(title: 'RequiredProperties', version: '1.0')]
class RequiredPropertiesApi
{
}

#[OA\Schema(component: 'RequiredPropertiesInferred')]
class RequiredPropertiesInferred
{
    #[OA\Property]
    public int $id;

    #[OA\Property]
    public ?string $nickname;

    #[OA\Property]
    public string $status = 'new';
}

#[OA\Schema(component: 'RequiredPropertiesDeclared')]
class RequiredPropertiesDeclared
{
    #[OA\Property]
    public int $id;

    #[OA\Property(required: true)]
    public ?string $nickname;

    #[OA\Property(required: true)]
    public string $status = 'new';

    #[OA\Property(required: false)]
    public int $legacyId;

    #[OA\Property(property: 'age', required: true)]
    public function getAge(): int
    {
        return 0;
    }
}

#[OA\Operation\Get(path: '/pets/{id}', operationId: 'getPet')]
#[OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'integer'))]
#[OA\Response(
    response: 200,
    description: 'The pet, with its links',
    content: new OA\MediaType\Json(
        properties: [
            new OA\Property(property: 'pet', schema: new OA\Schema\Ref(RequiredPropertiesDeclared::class), required: true),
            new OA\Property(property: 'links', schema: new OA\Schema(type: 'object')),
        ],
    ),
)]
class RequiredPropertiesController
{
}
