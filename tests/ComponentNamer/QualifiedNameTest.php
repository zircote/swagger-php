<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\ComponentNamer;

use OpenApi\ComponentNamer\QualifiedName;
use OpenApi\OpenApiException;
use OpenApi\Spec as OA;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class QualifiedNameTest extends TestCase
{
    /**
     * @return iterable<string, array{QualifiedName, string}>
     */
    public static function nameCases(): iterable
    {
        yield 'default' => [new QualifiedName(), 'OpenApi.Tests.Fixtures.Augmenter.TypeSchema'];
        yield 'stripped' => [new QualifiedName(strip: 'OpenApi\Tests\Fixtures\\'), 'Augmenter.TypeSchema'];
        yield 'stripped without the trailing separator' => [new QualifiedName(strip: '\OpenApi\Tests\Fixtures'), 'Augmenter.TypeSchema'];
        yield 'run together' => [new QualifiedName(strip: 'OpenApi\Tests\Fixtures\\', separator: ''), 'AugmenterTypeSchema'];
        yield 'outside the stripped namespace' => [new QualifiedName(strip: 'App\Dto\\', separator: '_'), 'OpenApi_Tests_Fixtures_Augmenter_TypeSchema'];
    }

    #[DataProvider('nameCases')]
    public function testNamesAClassSchema(QualifiedName $namer, string $expected): void
    {
        $schema = (new OA\Schema())->setReflector(new \ReflectionClass(Fixtures\Augmenter\TypeSchema::class));

        $this->assertSame($expected, $namer->name($schema));
    }

    public function testLeavesAComponentWithoutAClassInline(): void
    {
        $this->assertNull((new QualifiedName())->name(new OA\Schema()));
    }

    public function testRejectsASeparatorAComponentKeyCannotHold(): void
    {
        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessage('Invalid separator "\"');

        new QualifiedName(separator: '\\');
    }
}
