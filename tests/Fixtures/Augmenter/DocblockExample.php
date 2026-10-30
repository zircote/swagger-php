<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Schema]
class DocblockExample
{
    /**
     * @example 42
     */
    #[OA\Property]
    public int $value;

    /**
     * @example false
     */
    #[OA\Property]
    public bool $enabled;

    /**
     * @example {"id": 1}
     */
    #[OA\Property(schema: new OA\Schema(type: 'object'))]
    public array $extra;

    /**
     * @var list<int>
     * @example [1, 2]
     */
    #[OA\Property]
    public array $items;

    /**
     * @example {}
     */
    #[OA\Property(schema: new OA\Schema(type: 'object'))]
    public array $emptyObject;

    /**
     * @var list<int>
     * @example []
     */
    #[OA\Property]
    public array $emptyArray;

    /**
     * @example null
     */
    #[OA\Property]
    public ?int $nullable;

    /**
     * @example 00123
     */
    #[OA\Property]
    public string $code;

    /**
     * @example false
     */
    #[OA\Property]
    public string $flag;

    /**
     * @example 123
     */
    #[OA\Property]
    public string $numericCode;

    /**
     * @example 42
     */
    #[OA\Property(schema: new OA\Schema(example: 9))]
    public int $explicit;
}

#[OA\Info(title: 'Docblock examples', version: '1.0')]
#[OA\Operation\Get(
    path: '/examples',
    responses: [
        new OA\Response(
            response: 200,
            description: 'OK',
            content: new OA\Schema(ref: DocblockExample::class),
        ),
    ],
)]
class DocblockExampleController
{
}
