<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Type;

use OpenApi\Augmenter;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures\Type\KeywordCase;
use OpenApi\Tests\Fixtures\Type\Number;
use OpenApi\Type\TypeResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DocblockKeywordCaseTest extends TestCase
{
    use ExpectsLogEntries;

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function keywordCases(): iterable
    {
        yield 'String' => ['string', 'string'];
        yield 'Integer' => ['integer', 'integer'];
        yield 'Boolean' => ['boolean', 'boolean'];
        yield 'Double' => ['double', 'number'];
        yield 'Array of String' => ['stringArray', 'array'];
        yield 'nullable Int' => ['nullableInt', 'integer'];
    }

    #[DataProvider('keywordCases')]
    public function testKeywordInAnyCaseKeepsItsType(string $property, string $expected): void
    {
        $resolver = new TypeResolver();
        $resolver->setLogger($this->trackingLogger());

        $type = $resolver->resolve(new \ReflectionProperty(KeywordCase::class, $property));

        $this->assertSame($expected, $type?->type);
    }

    public function testClassNamedLikeAnAliasStaysAClass(): void
    {
        $type = (new TypeResolver())->resolve(new \ReflectionProperty(KeywordCase::class, 'number'));

        $this->assertSame(Number::class, $type?->type);
    }

    public function testUnreadableDocblockTypeIsReported(): void
    {
        $resolver = new TypeResolver();
        $resolver->setLogger($this->trackingLogger());
        $this->expectLogEntry('Unsupported docblock type "NoSuchClass" on ' . KeywordCase::class . '::$unknown', 'warning');

        $this->assertNull($resolver->resolve(new \ReflectionProperty(KeywordCase::class, 'unknown')));
    }

    public function testAResolverSetAfterTheLoggerReportsToo(): void
    {
        $types = new Augmenter\Types();
        $types->setLogger($this->trackingLogger());
        $resolver = new TypeResolver();
        $types->setTypeResolver($resolver);
        $this->expectLogEntry('Unsupported docblock type "NoSuchClass" on ' . KeywordCase::class . '::$unknown', 'warning');

        $resolver->resolve(new \ReflectionProperty(KeywordCase::class, 'unknown'));
    }
}
