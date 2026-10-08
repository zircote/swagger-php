<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Tests\Fixtures\Augmenter\PathItemInheritedController;
use OpenApi\Tests\Fixtures\Augmenter\PathItemUserController;
use OpenApi\Utils\SourceLocation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SourceLocationTest extends TestCase
{
    /**
     * @return iterable<string, array{\Reflector, string|null}>
     */
    public static function qualifiedMethods(): iterable
    {
        yield 'a method' => [new \ReflectionMethod(PathItemUserController::class, 'list'), PathItemUserController::class . '::list'];

        yield 'an inherited method names the class declaring it' => [new \ReflectionMethod(PathItemInheritedController::class, 'list'), PathItemUserController::class . '::list'];

        yield 'a parameter is in its method' => [new \ReflectionParameter([PathItemUserController::class, 'get'], 'id'), PathItemUserController::class . '::get'];

        yield 'a class is not in a method' => [new \ReflectionClass(PathItemUserController::class), null];
    }

    #[DataProvider('qualifiedMethods')]
    public function testQualifiedMethod(\Reflector $reflector, ?string $expected): void
    {
        $this->assertSame($expected, SourceLocation::fromReflector($reflector)->qualifiedMethod());
    }

    public function testAnUnknownLocationHasNoQualifiedMethod(): void
    {
        $this->assertNull((new SourceLocation())->qualifiedMethod());
    }
}
