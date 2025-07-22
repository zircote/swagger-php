<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Annotations as OA;
use OpenApi\Generator;

class ScratchTest extends OpenApiTestCase
{
    public static function scratchTestProvider(): iterable
    {
        foreach (glob(static::fixture('Scratch/*.php')) as $fixture) {
            $name = pathinfo($fixture, PATHINFO_FILENAME);

            if (0 === strpos($name, 'Abstract')) {
                continue;
            }

            $scratch = static::fixture("Scratch/$name.php");
            $specs = [
                static::fixture("Scratch/{$name}3.1.0.yaml") => OA\OpenApi::VERSION_3_1_0,
                static::fixture("Scratch/{$name}3.0.0.yaml") => OA\OpenApi::VERSION_3_0_0,
            ];

            $expectedLogs = [
                'Examples-3.0.0' => ['@OA\Schema() is only allowed for 3.1.x'],
            ];

            foreach ($specs as $spec => $version) {
                if (file_exists($spec)) {
                    $dataSet = "$name-$version";
                    yield $dataSet => [
                        $scratch,
                        $spec,
                        $version,
                        array_key_exists($dataSet, $expectedLogs) ? $expectedLogs[$dataSet] : [],
                    ];
                }
            }
        }
    }

    /**
     * Test scratch fixtures.
     *
     * @dataProvider scratchTestProvider
     */
    public function testScratch(string $scratch, string $spec, string $version, array $expectedLogs): void
    {
        foreach ($expectedLogs as $logLine) {
            $this->assertOpenApiLogEntryContains($logLine);
        }

        require_once $scratch;

        $openapi = (new Generator($this->getTrackingLogger()))
            ->setTypeResolver($this->getTypeResolver())
            ->setVersion($version)
            ->generate([$scratch]);

        // file_put_contents($spec, $openapi->toYaml());
        $this->assertSpecEquals($openapi, file_get_contents($spec));
    }
}
