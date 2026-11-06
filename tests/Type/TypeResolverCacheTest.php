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

    public function testAClassSourceFileIsReadOnce(): void
    {
        $class = 'UseStatements' . bin2hex(random_bytes(4));
        $file = sys_get_temp_dir() . "/{$class}.php";
        file_put_contents($file, <<<PHP
            <?php

            namespace OpenApi\\Tests\\Fixtures\\Type;

            use OpenApi\\Tests\\Fixtures\\Type\\KeywordCase as Collection;

            final class {$class}
            {
                /** @var Collection */
                public \$first;

                /** @var Collection */
                public \$second;
            }
            PHP);
        require $file;
        $class = "OpenApi\\Tests\\Fixtures\\Type\\{$class}";
        $this->assertTrue(class_exists($class));
        $resolver = new TypeResolver();

        $first = $resolver->resolve(new \ReflectionProperty($class, 'first'));
        unlink($file);
        $second = $resolver->resolve(new \ReflectionProperty($class, 'second'));

        $this->assertSame(KeywordCase::class, $first?->type);
        $this->assertSame(KeywordCase::class, $second?->type);
    }

    public function testNativeSelfAndParentResolveWithoutTheSourceFile(): void
    {
        $class = 'NativeContext' . bin2hex(random_bytes(4));
        $file = sys_get_temp_dir() . "/{$class}.php";
        file_put_contents($file, <<<PHP
            <?php

            namespace OpenApi\\Tests\\Fixtures\\Type;

            class {$class}Base
            {
            }

            final class {$class} extends {$class}Base
            {
                public self \$self;

                public parent \$parent;
            }
            PHP);
        require $file;
        unlink($file);
        $class = "OpenApi\\Tests\\Fixtures\\Type\\{$class}";
        $this->assertTrue(class_exists($class));
        $resolver = new TypeResolver();

        $this->assertSame($class, $resolver->resolve(new \ReflectionProperty($class, 'self'))?->type);
        $this->assertSame("{$class}Base", $resolver->resolve(new \ReflectionProperty($class, 'parent'))?->type);
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
