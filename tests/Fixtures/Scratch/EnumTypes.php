<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

// an enum used as a type with no schema of its own becomes one, named after it; hybrid only,
// classic leaves the type out
enum EnumTypesLanguage: string
{
    case English = 'en';
    case German = 'de';
}

enum EnumTypesPriority: int
{
    case Low = 1;
    case High = 2;
}

enum EnumTypesSuit
{
    case Hearts;
    case Spades;
}

// an enum with a schema of its own keeps it, case names and all
#[OAT\Schema]
enum EnumTypesColour: string
{
    case Red = 'red';
    case Blue = 'blue';
}

#[OAT\Schema(schema: 'EnumTypesHolder')]
class EnumTypesHolder
{
    #[OAT\Property]
    public EnumTypesLanguage $language;

    #[OAT\Property]
    public ?EnumTypesSuit $suit;

    /** @var list<EnumTypesPriority> */
    #[OAT\Property]
    public array $priorities;

    #[OAT\Property]
    public EnumTypesColour $colour;
}

#[OAT\Info(title: 'EnumTypes', version: '1.0')]
class EnumTypesEndpoint
{
    #[OAT\Get(
        path: '/items/{language}',
        operationId: 'EnumTypesItems',
        responses: [
            new OAT\Response(
                response: 200,
                description: 'The items',
                content: new OAT\JsonContent(ref: EnumTypesHolder::class)
            ),
        ]
    )]
    public function items(
        #[OAT\PathParameter] EnumTypesLanguage $language,
        #[OAT\QueryParameter] ?EnumTypesPriority $priority = null,
    ): void {
    }
}
