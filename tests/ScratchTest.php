<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Augmenter\Cleanup;
use OpenApi\Builder;
use OpenApi\Generator;
use OpenApi\Tests\Concerns\GeneratesTestMatrix;
use OpenApi\Type\LegacyTypeResolver;
use OpenApi\TypeResolverInterface;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\Attributes\DataProvider;

final class ScratchTest extends OpenApiTestCase
{
    use GeneratesTestMatrix;

    public static function scratchTestCases(): iterable
    {
        $basePath = self::fixture('Scratch');
        $phpVersion = self::phpVersion();

        // Keyed `{fixture}-{version}`, applying to every mode, or `{fixture}-{version}-{mode}`
        // for a diagnostic only one mode raises. Both keys contribute when both are present.
        $expectedLogs = [
            'Auth-3.0.0' => ['mutualTLS security schemes are not supported in OpenAPI 3.0 and will be omitted'],
            'Examples-3.0.0-classic' => ['@OA\Schema::examples is only allowed as of 3.1.0'],
            'Examples-3.0.0-spec' => ['examples array is not supported in OpenAPI 3.0'],
            'Examples-3.0.0-hybrid' => ['examples array is not supported in OpenAPI 3.0'],
            'Docblocks-3.0.0-spec' => ['const is not supported in OpenAPI 3.0'],
            'Docblocks-3.0.0-hybrid' => ['const is not supported in OpenAPI 3.0'],
            'Tags-3.2.0-spec' => ['references non-existent parent'],
            'Tags-3.2.0-hybrid' => ['references non-existent parent'],
        ];

        foreach (self::discoverFixtures("{$basePath}/*.php") as $scratchName => $scratch) {
            if (str_contains($scratchName, '-spec')) {
                continue;
            }

            $resolvers = self::getTypeResolvers();

            foreach (self::matrixCombinations(
                [
                    'version' => self::versions(),
                    'resolverName' => array_keys($resolvers),
                    'mode' => [Builder\Mode::CLASSIC, Builder\Mode::HYBRID, Builder\Mode::SPEC],
                ],
                [
                    fn (array $c): bool => $c['mode'] !== Builder\Mode::CLASSIC
                        && $resolvers[$c['resolverName']] instanceof LegacyTypeResolver,
                ],
            ) as $combo) {
                $source = $scratch;
                if ($combo['mode'] === Builder\Mode::SPEC) {
                    $source = str_replace('.php', '-spec.php', $scratch);
                    if (!file_exists($source)) {
                        continue;
                    }
                }

                // Hybrid bridges the classic annotations into the spec compilers, so it is held
                // to the spec expectation where the fixture has a spec pair, and to classic's
                // otherwise. A `-hybrid` file overrides both, for the few documents the two
                // pipelines render differently.
                $spec = self::mostSpecific([
                    "{$basePath}/{$scratchName}{$combo['version']}-{$combo['resolverName']}-{$combo['mode']->value}.yaml",
                    "{$basePath}/{$scratchName}{$combo['version']}-{$combo['mode']->value}.yaml",
                    ...$combo['mode'] === Builder\Mode::HYBRID ? [
                        "{$basePath}/{$scratchName}{$combo['version']}-{$combo['resolverName']}-spec.yaml",
                        "{$basePath}/{$scratchName}{$combo['version']}-spec.yaml",
                    ] : [],
                    "{$basePath}/{$scratchName}{$combo['version']}-{$combo['resolverName']}.yaml",
                    "{$basePath}/{$scratchName}{$combo['version']}-{$phpVersion}.yaml",
                    "{$basePath}/{$scratchName}{$combo['version']}.yaml",
                ]);

                if ($spec === null) {
                    continue;
                }

                $key = self::matrixKey([$combo['resolverName'], $scratchName, $combo['version'], $phpVersion, $combo['mode']->value]);
                $logKey = "{$scratchName}-{$combo['version']}";

                yield $key => [
                    $resolvers[$combo['resolverName']],
                    $source,
                    $combo['mode'],
                    $spec,
                    $combo['version'],
                    array_merge($expectedLogs[$logKey] ?? [], $expectedLogs["{$logKey}-{$combo['mode']->value}"] ?? []),
                ];
            }
        }
    }

    #[DataProvider('scratchTestCases')]
    public function testScratch(TypeResolverInterface $typeResolver, string $scratch, Builder\Mode $mode, string $spec, string $version, array $expectedLogs): void
    {
        foreach ($expectedLogs as $logLine) {
            $this->expectLogEntry($logLine);
        }

        require_once $scratch;

        $result = (new Builder())
            ->setMode($mode)
            ->addSource($scratch)
            ->setVersion($version)
            ->setLogger($this->trackingLogger())
            ->withAugmenters(function (Pipeline $pipeline): void {
                $pipeline->get(Cleanup::class)->setEnabled(false);
            })
            ->withGenerator(fn (Generator $generator): Generator => $generator
                ->setTypeResolver($typeResolver)
                ->setConfig(['mergeIntoOpenApi' => ['mergeComponents' => true]]))
            ->build();

        // file_put_contents($spec, $result->toYaml());
        $this->assertSpecEquals($result->toYaml(), file_get_contents($spec));
    }
}
