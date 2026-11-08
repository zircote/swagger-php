<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Augmenter\Cleanup;
use OpenApi\Builder;
use OpenApi\Generator;
use OpenApi\Tests\Concerns\GeneratesTestMatrix;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\Attributes\DataProvider;

final class ScratchTest extends OpenApiTestCase
{
    use GeneratesTestMatrix;

    /**
     * @return iterable<mixed>
     */
    public static function scratchTestCases(): iterable
    {
        $basePath = self::fixture('Scratch');
        $phpVersion = self::phpVersion();

        // one per alias; classic maps them without a word
        $typeAliases = array_map(
            static fn (string $alias): string => "Schema type \"{$alias}\" is a classic alias",
            ['long', 'int', 'bool', 'float', 'double', 'date', 'DateTime'],
        );

        // a property typed with a class that is no component is left untyped, with a word
        $classRef = static fn (string $suffix): array => array_map(
            static fn (string $property): string => "Type \"OpenApi\\Tests\\Fixtures\\Scratch\\ClassRefPlain{$suffix}\" in OpenApi\\Tests\\Fixtures\\Scratch\\ClassRefHolder{$suffix}::\${$property} matches no schema, omitted",
            ['plain', 'nullablePlain', 'plainList', 'plainMap', 'plainShape', 'plainUnion'],
        );

        // Keyed `{fixture}-{version}`, applying to every mode, or `{fixture}-{version}-{mode}`
        // for a diagnostic only one mode raises. Both keys contribute when both are present.
        $expectedLogs = [
            'Auth-3.0.0-classic' => ['mutualTLS security schemes are not supported in OpenAPI 3.0 and will be omitted'],
            'Auth-3.0.0-spec' => ['Security schemes of type mutualTLS are not supported in OpenAPI 3.0, omitted'],
            'Auth-3.0.0-hybrid' => ['Security schemes of type mutualTLS are not supported in OpenAPI 3.0, omitted'],
            'SchemaKeywords-3.0.0-classic' => [
                'prefixItems is not supported in OpenAPI 3.0',
                'unevaluatedProperties is not supported in OpenAPI 3.0',
                'unevaluatedItems is not supported in OpenAPI 3.0',
                'if/then/else is not supported in OpenAPI 3.0',
                '@OA\Items() is required when @OA\Schema() has type "array"',
            ],
            'SchemaKeywords-3.0.0-spec' => [
                'uses prefixItems, not supported in OpenAPI 3.0',
                'uses unevaluatedProperties, not supported in OpenAPI 3.0',
                'uses unevaluatedItems, not supported in OpenAPI 3.0',
                'uses if/then/else, not supported in OpenAPI 3.0',
                'has type "array" but no items',
                'Schema "envelope" uses contentEncoding/contentMediaType, not supported in OpenAPI 3.0, omitted',
            ],
            'SchemaKeywords-3.0.0-hybrid' => [
                'uses prefixItems, not supported in OpenAPI 3.0',
                'uses unevaluatedProperties, not supported in OpenAPI 3.0',
                'uses unevaluatedItems, not supported in OpenAPI 3.0',
                'uses if/then/else, not supported in OpenAPI 3.0',
                'has type "array" but no items',
                'Schema "envelope" uses contentEncoding/contentMediaType, not supported in OpenAPI 3.0, omitted',
            ],
            'Examples-3.0.0-classic' => ['@OA\Schema::examples is only allowed as of 3.1.0'],
            'Examples-3.0.0-spec' => ['uses an examples array, not supported in OpenAPI 3.0'],
            'Examples-3.0.0-hybrid' => ['uses an examples array, not supported in OpenAPI 3.0'],
            'NullRef-3.0.0-classic' => [
                '@OA\Property::examples is only allowed as of 3.1.0',
                '@OA\Property::examples is only allowed as of 3.1.0',
            ],
            'NullRef-3.0.0-spec' => [
                'uses an examples array, not supported in OpenAPI 3.0, using the first value as example',
                'sets examples beside $ref "#/components/schemas/repository", not supported in OpenAPI 3.0, omitted',
                'sets title, description, example, deprecated, readOnly, default beside $ref "#/components/schemas/repository", omitted',
                'sets writeOnly beside $ref "#/components/schemas/repository", omitted',
            ],
            'NullRef-3.0.0-hybrid' => [
                'uses an examples array, not supported in OpenAPI 3.0, using the first value as example',
                'sets examples beside $ref "#/components/schemas/repository", not supported in OpenAPI 3.0, omitted',
                'sets title, description, example, deprecated, readOnly, default beside $ref "#/components/schemas/repository", omitted',
                'sets writeOnly beside $ref "#/components/schemas/repository", omitted',
            ],
            'CustomAttributes-3.0.0-spec' => ['sets title beside $ref "#/components/schemas/CAItemModel", omitted'],
            'CustomAttributes-3.0.0-hybrid' => ['sets title beside $ref "#/components/schemas/CAItemModel", omitted'],
            'ComplexCustomAttributes-3.0.0-hybrid' => [
                'sets title beside $ref "#/components/schemas/TargetId", omitted',
                'sets title beside $ref "#/components/schemas/TargetType", omitted',
            ],
            'NestedSchema-3.0.0-spec' => [
                'sets description beside $ref "#/components/schemas/NestedSchemaTwo", omitted',
                'sets description beside $ref "#/components/schemas/MultipleOneOf", omitted',
            ],
            'NestedSchema-3.0.0-hybrid' => [
                'sets description beside $ref "#/components/schemas/NestedSchemaTwo", omitted',
                'sets description beside $ref "#/components/schemas/MultipleOneOf", omitted',
            ],
            'Docblocks-3.0.0-spec' => ['uses const, not supported in OpenAPI 3.0'],
            'Docblocks-3.0.0-hybrid' => ['uses const, not supported in OpenAPI 3.0'],
            'ServerVariableEnumClassString-3.0.0' => ['ServerVariable(secure): enum values must be strings, dropping true', 'ServerVariable(secure): enum values must be strings, dropping false'],
            'SecurityShapes-3.0.0-hybrid' => ['Security requirement "bearer" is a bare scheme name'],
            'SecurityShapes-3.1.0-hybrid' => ['Security requirement "bearer" is a bare scheme name'],
            'SecurityShapes-3.2.0-hybrid' => ['Security requirement "bearer" is a bare scheme name'],
            'ServerVariableEnumClassString-3.1.0' => ['ServerVariable(secure): enum values must be strings, dropping true', 'ServerVariable(secure): enum values must be strings, dropping false'],
            'ServerVariableEnumClassString-3.2.0' => ['ServerVariable(secure): enum values must be strings, dropping true', 'ServerVariable(secure): enum values must be strings, dropping false'],
            'Tags-3.2.0-spec' => ['which does not exist'],
            'TypeAliases-3.0.0-spec' => $typeAliases,
            'TypeAliases-3.0.0-hybrid' => $typeAliases,
            'TypeAliases-3.1.0-spec' => $typeAliases,
            'TypeAliases-3.1.0-hybrid' => $typeAliases,
            'TypeAliases-3.2.0-spec' => $typeAliases,
            'TypeAliases-3.2.0-hybrid' => $typeAliases,
            'Tags-3.2.0-hybrid' => ['which does not exist'],
            'ClassRef-3.0.0-spec' => $classRef('Spec'),
            'ClassRef-3.0.0-hybrid' => $classRef(''),
            'ClassRef-3.1.0-spec' => $classRef('Spec'),
            'ClassRef-3.1.0-hybrid' => $classRef(''),
            'ClassRef-3.2.0-spec' => $classRef('Spec'),
            'ClassRef-3.2.0-hybrid' => $classRef(''),
        ];

        foreach (self::discoverFixtures("{$basePath}/*.php") as $scratchName => $scratch) {
            if (str_contains($scratchName, '-spec')) {
                continue;
            }

            foreach (self::matrixCombinations(
                [
                    'version' => self::versions(),
                    'mode' => [Builder\Mode::CLASSIC, Builder\Mode::HYBRID, Builder\Mode::SPEC],
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
                // pipelines render differently. A PHP version narrows a mode's file as it does
                // the shared one.
                $spec = self::mostSpecific([
                    "{$basePath}/{$scratchName}{$combo['version']}-{$phpVersion}-{$combo['mode']->value}.yaml",
                    "{$basePath}/{$scratchName}{$combo['version']}-{$combo['mode']->value}.yaml",
                    ...$combo['mode'] === Builder\Mode::HYBRID ? [
                        "{$basePath}/{$scratchName}{$combo['version']}-{$phpVersion}-spec.yaml",
                        "{$basePath}/{$scratchName}{$combo['version']}-spec.yaml",
                    ] : [],
                    "{$basePath}/{$scratchName}{$combo['version']}-{$phpVersion}.yaml",
                    "{$basePath}/{$scratchName}{$combo['version']}.yaml",
                ]);

                if ($spec === null) {
                    continue;
                }

                $key = self::matrixKey([$scratchName, $combo['version'], $phpVersion, $combo['mode']->value]);
                $logKey = "{$scratchName}-{$combo['version']}";

                yield $key => [
                    $source,
                    $combo['mode'],
                    $spec,
                    $combo['version'],
                    array_merge($expectedLogs[$logKey] ?? [], $expectedLogs["{$logKey}-{$combo['mode']->value}"] ?? []),
                ];
            }
        }
    }

    /**
     * @param array<mixed> $expectedLogs
     */
    #[DataProvider('scratchTestCases')]
    public function testScratch(string $scratch, Builder\Mode $mode, string $spec, string $version, array $expectedLogs): void
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
                ->setTypeResolver($this->getTypeResolver())
                ->setConfig(['mergeIntoOpenApi' => ['mergeComponents' => true]]))
            ->build();

        // file_put_contents($spec, $result->toYaml());
        $this->assertSpecEquals($result->toYaml(), file_get_contents($spec));
    }
}
