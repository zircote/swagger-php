<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Annotations as OA;
use OpenApi\Tests\Concerns\UsesExamples;
use OpenApi\Util;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

class UtilTest extends OpenApiTestCase
{
    use UsesExamples;

    public function testRefEncode(): void
    {
        $this->assertSame('#/paths/~1blogs~1{blog_id}~1new~0posts', '#/paths/' . Util::refEncode('/blogs/{blog_id}/new~posts'));
    }

    public function testRefDecode(): void
    {
        $this->assertSame('/blogs/{blog_id}/new~posts', Util::refDecode('~1blogs~1{blog_id}~1new~0posts'));
    }

    public function testFinder(): void
    {
        // Create a finder for one of the example directories that has a subdirectory.
        $finder = (new Finder())->in($this->examplePath('using-traits/annotations'));
        $this->assertGreaterThan(0, iterator_count($finder), 'There should be at least a few files and a directory.');
        $finder_array = \iterator_to_array($finder);
        $directory_path = Path::normalize($this->examplePath('using-traits/annotations/Decoration'));
        $normalizePathKeys = function ($paths) {
            return \array_combine(
                \array_map(
                    function ($path) {
                        return Path::normalize($path);
                    },
                    \array_keys($paths)
                ),
                \array_values($paths)
            );
        };
        $this->assertArrayHasKey($directory_path, $normalizePathKeys($finder_array), 'The directory should be a path in the finder.');
        // Use the Util method that should set the finder to only find files, since swagger-php only needs files.
        $finder_result = Util::finder($finder);
        $this->assertGreaterThan(0, iterator_count($finder_result), 'There should be at least a few file paths.');
        $finder_result_array = \iterator_to_array($finder_result);
        $this->assertArrayNotHasKey($directory_path, $normalizePathKeys($finder_result_array), 'The directory should not be a path in the finder.');
    }

    public static function shortenFixtures(): iterable
    {
        return [
            [[OA\Get::class], ['@OA\Get']],
            [[OA\Get::class, OA\Post::class], ['@OA\Get', '@OA\Post']],
        ];
    }

    /**
     * @dataProvider shortenFixtures
     */
    public function testShorten(array $classes, array $expected): void
    {
        $this->assertEquals($expected, Util::shorten($classes));
    }
}
