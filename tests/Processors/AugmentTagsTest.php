<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Tests\OpenApiTestCase;

class AugmentTagsTest extends OpenApiTestCase
{
    /**
     * @requires PHP 8.1
     */
    public function testAugmentTags(): void
    {
        $this->skipLegacy();

        $config = [
            'pathFilter' => ['paths' => ['#^/hello/#']],
            'cleanUnusedComponents' => ['enabled' => true],
        ];
        $analysis = $this->analysisFromFixtures(['SurplusTag.php'], static::processors(), null, $config);

        $this->assertCount(1, $analysis->openapi->tags);
    }
}
