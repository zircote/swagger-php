<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\TypeResolverInterface;
use PHPUnit\Framework\Attributes\DataProvider;

final class ScratchTest extends OpenApiTestCase
{
    public static function scratchTestCases(): iterable
    {
        // scratch (.php) iterator
        $scratchIterator = function (): iterable {
            foreach (glob(self::fixture('Scratch/*.php')) as $fixture) {
                $name = pathinfo($fixture, PATHINFO_FILENAME);

                if (str_starts_with($name, 'Abstract')) {
                    continue;
                }

                yield $name => self::fixture("Scratch/{$name}.php");
            }
        };

        // spec iterator (most specific) for a given scratch name
        $specIterator = function (string $scratchName): iterable {
            foreach ([OA\OpenApi::VERSION_3_2_0, OA\OpenApi::VERSION_3_1_0, OA\OpenApi::VERSION_3_0_0] as $version) {
                foreach (self::getTypeResolvers() as $resolverName => $typeResolver) {
                    $phpVersion = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
                    $caseName = "{$resolverName}-{$scratchName}-{$version}-{$phpVersion}";
                    $specs = [
                        self::fixture("Scratch/{$scratchName}{$version}{$resolverName}-{$phpVersion}.yaml"),
                        self::fixture("Scratch/{$scratchName}{$version}-{$phpVersion}.yaml"),
                        self::fixture("Scratch/{$scratchName}{$version}{$resolverName}.yaml"),
                        self::fixture("Scratch/{$scratchName}{$version}.yaml"),
                    ];
                    foreach ($specs as $spec) {
                        if (file_exists($spec)) {
                            yield $caseName => [
                                'spec' => $spec,
                                'typeResolver' => $typeResolver,
                                'version' => $version,
                            ];
                            break;
                        }
                    }
                }
            }
        };

        $expectedLogs = [
            'Examples-3.0.0' => ['@OA\Schema() is only allowed as of 3.1.0'],
        ];

        foreach ($scratchIterator() as $scratchName => $scratch) {
            foreach ($specIterator($scratchName) as $caseName => $details) {
                yield $caseName => [
                    $details['typeResolver'],
                    $scratch,
                    $details['spec'],
                    $details['version'],
                    array_key_exists($caseName, $expectedLogs) ? $expectedLogs[$caseName] : [],
                ];
            }
        }
    }

    /**
     * Test scratch fixtures.
     */
    #[DataProvider('scratchTestCases')]
    public function testScratch(TypeResolverInterface $typeResolver, string $scratch, string $spec, string $version, array $expectedLogs): void
    {
        foreach ($expectedLogs as $logLine) {
            $this->assertOpenApiLogEntryContains($logLine);
        }

        require_once $scratch;

        $openapi = (new Generator($this->getTrackingLogger()))
            ->setTypeResolver($typeResolver)
            ->setVersion($version)
            ->setConfig(['mergeIntoOpenApi' => ['mergeComponents' => true]])
            ->generate([$scratch]);

        // file_put_contents($spec, $openapi->toYaml());
        $this->assertSpecEquals($openapi, file_get_contents($spec));
    }
}
