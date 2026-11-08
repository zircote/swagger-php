<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Type;

use OpenApi\Tests\Fixtures\Type\KeywordCase;
use OpenApi\Type\TypeResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\TypeContext\TypeContextFactory;

final class TypeResolverCacheTest extends TestCase
{
    public function testAClassSourceFileIsReadOnce(): void
    {
        if (!property_exists(TypeContextFactory::class, 'usesCache')) {
            $this->markTestSkipped('This version of symfony/type-info does not cache use statements.');
        }

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

    public function testNativeSelfAndParentResolve(): void
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
        try {
            require $file;
            $class = "OpenApi\\Tests\\Fixtures\\Type\\{$class}";
            $this->assertTrue(class_exists($class));
            $resolver = new TypeResolver();

            $this->assertSame($class, $resolver->resolve(new \ReflectionProperty($class, 'self'))?->type);
            $this->assertSame("{$class}Base", $resolver->resolve(new \ReflectionProperty($class, 'parent'))?->type);
        } finally {
            unlink($file);
        }
    }
}
