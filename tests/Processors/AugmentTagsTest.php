<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Generator;
use OpenApi\Tests\OpenApiTestCase;

final class AugmentTagsTest extends OpenApiTestCase
{
    public function testFilteredAugmentTags(): void
    {
        $config = [
            'pathFilter' => ['paths' => ['#^/hello/#']],
            'cleanUnusedComponents' => ['enabled' => true],
        ];
        $analysis = $this->analysisFromFixtures(
            ['SurplusTag.php'],
            $this->processorPipeline(),
            config: $config
        );

        $this->assertCount(1, $analysis->openapi->tags);
    }

    public function testDedupedAugmentTags(): void
    {
        $analysis = $this->analysisFromFixtures(
            ['SurplusTag.php'],
            $this->processorPipeline()
        );

        $this->assertCount(3, $analysis->openapi->tags, 'Expecting 3 unique tags');
    }

    public function testAllowUnusedTags(): void
    {
        $analysis = $this->analysisFromFixtures(
            ['UnusedTags.php'],
            $this->processorPipeline(),
            config: ['augmentTags' => ['whitelist' => ['fancy']]]
        );

        $this->assertCount(2, $analysis->openapi->tags, 'Expecting fancy tag to be preserved');
    }

    public function testAllowUnusedTagsWildcard(): void
    {
        $analysis = $this->analysisFromFixtures(
            ['UnusedTags.php'],
            $this->processorPipeline(),
            config: ['augmentTags' => ['whitelist' => ['*']]]
        );

        $this->assertCount(3, $analysis->openapi->tags, 'Expecting all tags to be preserved');
    }

    public function testWithoutDescriptions(): void
    {
        $analysis = $this->analysisFromFixtures(
            ['Processors/EntityControllerClass.php'],
            $this->processorPipeline(),
            null,
            [
                'augmentTags' => ['withDescription' => false],
            ]
        );

        $this->assertNotEmpty($analysis->openapi->tags);
        $firstTag = $analysis->openapi->tags[0];
        $this->assertEquals(Generator::UNDEFINED, $firstTag->description);
    }
}
