<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Type;

use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures\Type\Template;
use OpenApi\Type\TypeResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DocblockTemplateTest extends TestCase
{
    use ExpectsLogEntries;

    /**
     * @return iterable<string, array{string}>
     */
    public static function templateCases(): iterable
    {
        yield 'unbounded' => ['results'];
        yield 'bounded' => ['bounded'];
    }

    #[DataProvider('templateCases')]
    public function testClassTemplateIsNoUnknownClass(string $property): void
    {
        $resolver = new TypeResolver();
        $resolver->setLogger($this->trackingLogger());

        $type = $resolver->resolve(new \ReflectionProperty(Template::class, $property));

        $this->assertSame('array', $type?->type);
    }
}
