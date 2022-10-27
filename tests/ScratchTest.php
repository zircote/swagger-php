<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Annotations\OpenApi;
use OpenApi\Generator;

class ScratchTest extends OpenApiTestCase
{
    public function scratchTests(): iterable
    {
        foreach (glob($this->fixture('Scratch/*.php')) as $fixture) {
            $name = pathinfo($fixture, PATHINFO_FILENAME);
            yield $name => [
                OpenApi::VERSION_3_0_0,
                $this->fixture("Scratch/$name.php"),
                $this->fixture("Scratch/$name.yaml"),
                [],
            ];
        }
    }

    /**
     * Test scratch fixtures.
     *
     * @dataProvider scratchTests
     *
     * @requires     PHP 7.4
     */
    public function testScratch(string $version, string $scratch, string $spec, array $expectedLog): void
    {
        foreach ($expectedLog as $logLine) {
            $this->assertOpenApiLogEntryContains($logLine);
        }

        require_once $scratch;

        $openapi = (new Generator($this->getTrackingLogger()))
            ->setVersion($version)
            ->generate([$scratch]);
        // file_put_contents($spec, $openapi->toYaml());
        $this->assertSpecEquals($openapi, file_get_contents($spec));
    }
}
