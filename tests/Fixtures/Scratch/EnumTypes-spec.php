<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

// an enum used as a type with no schema of its own becomes one, named after it
enum EnumTypesLanguageSpec: string
{
    case English = 'en';
    case German = 'de';
}

enum EnumTypesPrioritySpec: int
{
    case Low = 1;
    case High = 2;
}

enum EnumTypesSuitSpec
{
    case Hearts;
    case Spades;
}

// an enum with a schema of its own keeps it, case names and all
#[OA\Schema]
enum EnumTypesColourSpec: string
{
    case Red = 'red';
    case Blue = 'blue';
}

#[OA\Schema(component: 'EnumTypesHolder')]
class EnumTypesHolderSpec
{
    #[OA\Property]
    public EnumTypesLanguageSpec $language;

    #[OA\Property]
    public ?EnumTypesSuitSpec $suit;

    /** @var list<EnumTypesPrioritySpec> */
    #[OA\Property]
    public array $priorities;

    #[OA\Property]
    public EnumTypesColourSpec $colour;
}

#[OA\Info(title: 'EnumTypes', version: '1.0')]
class EnumTypesEndpointSpec
{
    #[OA\Operation\Get(path: '/items/{language}', operationId: 'EnumTypesItems')]
    #[OA\Response(
        response: 200,
        description: 'The items',
        content: new OA\MediaType\Json(ref: EnumTypesHolderSpec::class)
    )]
    public function items(
        #[OA\Parameter\Path] EnumTypesLanguageSpec $language,
        #[OA\Parameter\Query] ?EnumTypesPrioritySpec $priority = null,
    ): void {
    }
}
