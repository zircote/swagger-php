<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Type;

use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures\Type\KeywordCase;
use OpenApi\Type\TypeResolver;
use PHPUnit\Framework\TestCase;

final class TypeResolverCacheTest extends TestCase
{
    use ExpectsLogEntries;

    public function testAReflectorIsReportedOnce(): void
    {
        $resolver = new TypeResolver();
        $resolver->setLogger($this->trackingLogger());
        $this->expectLogEntry('Unsupported docblock type "NoSuchClass" on ' . KeywordCase::class . '::$unknown', 'warning');

        $reflector = new \ReflectionProperty(KeywordCase::class, 'unknown');
        $resolver->resolve($reflector);
        $resolver->resolve($reflector);
    }

    public function testEachAnswerIsACopy(): void
    {
        $resolver = new TypeResolver();
        $reflector = new \ReflectionProperty(KeywordCase::class, 'stringArray');

        $first = $resolver->resolve($reflector);
        $this->assertSame('string', $first?->items?->type);
        $first->nullable = true;
        $first->items->type = 'integer';

        $second = $resolver->resolve($reflector);
        $this->assertNull($second?->nullable);
        $this->assertSame('string', $second?->items?->type);
    }
}
