<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec;

use OpenApi\Contracts\RefInterface;
use OpenApi\Spec as OA;
use OpenApi\Tests\Fixtures\ClassDescriptions\Author;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RefInterfaceTest extends TestCase
{
    /**
     * @return iterable<string, array{RefInterface, string|null, string|null}>
     */
    public static function refs(): iterable
    {
        yield 'a class' => [new OA\Schema(ref: Author::class), Author::class, Author::class];

        yield 'a pointer' => [new OA\Response(ref: '#/components/responses/NotFound'), '#/components/responses/NotFound', null];

        yield 'no ref' => [new OA\Parameter(name: 'id'), null, null];

        yield 'a wrapped Ref' => [
            new OA\Schema(ref: new OA\Schema\Ref(ref: Author::class, qualifiers: ['groups' => ['summary']])),
            Author::class,
            Author::class . '{"groups":["summary"]}',
        ];

        yield 'a Ref itself' => [
            new OA\Schema\Ref(ref: Author::class, qualifiers: ['groups' => ['summary']]),
            Author::class,
            Author::class . '{"groups":["summary"]}',
        ];

        yield 'a wrapped pointer' => [new OA\Header(ref: new OA\Schema\Ref(ref: '#/components/headers/Rate')), '#/components/headers/Rate', null];
    }

    #[DataProvider('refs')]
    public function testValueAndKey(RefInterface $attribute, ?string $value, ?string $key): void
    {
        $this->assertSame($value, $attribute->getRefValue());
        $this->assertSame($key, $attribute->getRefKey()?->__toString());
    }

    public function testSetRefReplacesAWrappedRef(): void
    {
        $schema = new OA\Schema(ref: new OA\Schema\Ref(ref: Author::class));

        $schema->setRef('#/components/schemas/Author');

        $this->assertSame('#/components/schemas/Author', $schema->ref);
    }
}
