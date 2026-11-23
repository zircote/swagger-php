<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PathFilterTest extends TestCase
{
    /**
     * @return \Generator<string, array{list<string>, list<string>, list<string>, list<string>, list<string>}>
     */
    public static function filterProvider(): \Generator
    {
        yield 'no filter keeps all' => [[], [], [], [], ['/products', '/products/{id}', '/users', '/users/{id}']];
        yield 'filter by tag' => [['/^products$/'], [], [], [], ['/products', '/products/{id}']];
        yield 'filter by path' => [[], ['#^/users#'], [], [], ['/users', '/users/{id}']];
        yield 'tag or path (union)' => [['/admin/'], ['#/products$#'], [], [], ['/products', '/users/{id}']];
        yield 'no match' => [['/^nonexistent$/'], [], [], [], []];
        yield 'exclude by tag' => [[], [], ['/^admin$/'], [], ['/products', '/products/{id}', '/users']];
        yield 'exclude by path' => [[], [], [], ['#\{id\}$#'], ['/products', '/users']];
        yield 'exclude tag or path (union)' => [[], [], ['/^admin$/'], ['#^/products$#'], ['/products/{id}', '/users']];
        yield 'exclude after include' => [[], ['#^/users#'], ['/^admin$/'], [], ['/users']];
        yield 'exclude everything' => [[], [], [], ['#^/#'], []];
    }

    /**
     * @param list<string> $tags
     * @param list<string> $paths
     * @param list<string> $excludeTags
     * @param list<string> $excludePaths
     * @param list<string> $expectedPaths
     */
    #[DataProvider('filterProvider')]
    public function testFilter(array $tags, array $paths, array $excludeTags, array $excludePaths, array $expectedPaths): void
    {
        $spec = $this->createSpec();

        (new Augmenter\PathFilter(tags: $tags, paths: $paths, excludeTags: $excludeTags, excludePaths: $excludePaths))($spec);

        $actualPaths = array_map(fn (OA\Operation $op): ?string => $op->path, $spec->operations);
        $this->assertSame($expectedPaths, $actualPaths);
    }

    public function testSettersAreFluent(): void
    {
        $filter = new Augmenter\PathFilter();
        $result = $filter->setTags(['/test/'])->setPaths(['/path/'])->setExcludeTags(['/test/'])->setExcludePaths(['/path/']);

        $this->assertSame($filter, $result);
    }

    protected function createSpec(): Specification
    {
        $spec = new Specification();
        $spec->operations[] = new OA\Operation(path: '/products', method: 'get', tags: ['products']);
        $spec->operations[] = new OA\Operation(path: '/products/{id}', method: 'get', tags: ['products']);
        $spec->operations[] = new OA\Operation(path: '/users', method: 'get', tags: ['users']);
        $spec->operations[] = new OA\Operation(path: '/users/{id}', method: 'get', tags: ['users', 'admin']);

        return $spec;
    }
}
