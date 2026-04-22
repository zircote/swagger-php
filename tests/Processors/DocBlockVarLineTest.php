<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Processors\Concerns\DocblockTrait;
use OpenApi\Tests\OpenApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class DocBlockVarLineTest extends OpenApiTestCase
{
    use DocblockTrait;

    public static function varLineCases(): iterable
    {
        yield 'multi-line' => [
            <<<END
/**
 * @example Allan
 * @var null|string the second name of the customer
 */
END,
            [
                'type' => 'null|string',
                'description' => 'the second name of the customer',
            ],
        ];

        yield 'split-description' => [
            <<< END
/**
 * The unique identifier of a product in our catalog.
 *
 * @var int
 *
 * @OA\Property(format="int64", example=1)
 */
END,
            [
                'type' => 'int',
                'description' => null,
            ],
        ];

        yield 'single-full-line' => [
            '/* @var string|null $limit An optional limit */',
            [
                'type' => 'string|null',
                'description' => 'An optional limit',
            ],
        ];
    }

    #[DataProvider('varLineCases')]
    public function testDocBlockVarLine(string $comment, array $expected): void
    {
        $this->assertSame($expected, $this->parseVarLine($comment));
    }
}
