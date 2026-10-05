<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'YoYo')]
class ClassRef
{
}

#[OAT\Info(title: 'ClassRef', version: '1.0')]
#[OAT\Get(
    path: '/endpoint',
    operationId: 'ClassRefEndpoint',
    responses: [
        new OAT\Response(
            response: 200,
            description: 'All good',
            content: new OAT\JsonContent(ref: ClassRef::class)
        ),
    ]
)]
class ClassRefEndpoint
{
}

// a property typed with a class infers a reference only when the class is a component
class ClassRefPlain
{
}

#[OAT\Schema(schema: 'ClassRefHolder')]
class ClassRefHolder
{
    #[OAT\Property]
    public ClassRef $component;

    #[OAT\Property]
    public ClassRefPlain $plain;

    #[OAT\Property]
    public ?ClassRefPlain $nullablePlain;
}

#[OAT\Get(
    path: '/holder',
    operationId: 'ClassRefHolderEndpoint',
    responses: [
        new OAT\Response(
            response: 200,
            description: 'Holder',
            content: new OAT\JsonContent(ref: ClassRefHolder::class)
        ),
    ]
)]
class ClassRefHolderEndpoint
{
}
