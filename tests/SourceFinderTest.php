<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\SourceFinder;
use OpenApi\Tests\Concerns\UsesExamples;

final class SourceFinderTest extends OpenApiTestCase
{
    use UsesExamples;

    public function testNested(): void
    {
        $finder = (new SourceFinder(self::examplePath('using-traits/annotations')));
        $sources = iterator_to_array($finder);

        $this->assertCount(12, $sources, 'There should be at least a few files and a directory.');
        $this->assertArrayHasKey(self::examplePath('using-traits/annotations/Decoration/Whistles.php'), $sources);
    }

    public function testExclude(): void
    {
        $finder = (new SourceFinder(
            self::examplePath('using-traits/annotations'),
            self::examplePath('using-traits/annotations/Decoration')
        ));
        $sources = iterator_to_array($finder);

        $this->assertArrayNotHasKey(self::examplePath('using-traits/annotations/Decoration/Whistles.php'), $sources);
        $this->assertArrayNotHasKey(self::examplePath('using-traits/annotations/Decoration/Bell.php'), $sources);
    }
}
