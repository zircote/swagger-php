<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\SourceFinder;
use OpenApi\Tests\Concerns\UsesExamples;

class SourceFinderTest extends OpenApiTestCase
{
    use UsesExamples;

    public function testNested(): void
    {
        $finder = (new SourceFinder($this->examplePath('using-traits/annotations')));
        $sources = iterator_to_array($finder);

        $this->assertCount(12, $sources, 'There should be at least a few files and a directory.');
        $this->assertArrayHasKey($this->examplePath('using-traits/annotations/Decoration/Whistles.php'), $sources);
    }
}
