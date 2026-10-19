<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\HybridBridge;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

/**
 * A stacked `License` merges into the `Info` beside it, so it is not misplaced.
 */
#[OAT\Info(version: '1.0.0', title: 'Misplaced Attributes')]
#[OAT\License(name: 'MIT')]
class MisplacedAttributesApi
{
    #[OAT\Get(path: '/pets', responses: [new OAT\Response(response: 200, description: 'ok')])]
    public function pets(): void
    {
    }
}

/**
 * A `Contact` belongs inside `Info`, so beside a `Schema` nothing takes it.
 */
#[OAT\Schema(schema: 'MisplacedPet')]
#[OAT\Contact(name: 'misplaced')]
class MisplacedPet
{
    #[OAT\Property]
    public string $name;
}

/**
 * No schema takes in a property of a class that is not one.
 */
class MisplacedPetFilter
{
    #[OAT\Property]
    public string $filter;
}

/**
 * A docblock annotation nested in the wrong parent is left unmerged there.
 *
 * @OA\Schema(
 *     schema="MisplacedDocblock",
 *     @OA\Contact(name="nested")
 * )
 */
class MisplacedDocblock
{
}
