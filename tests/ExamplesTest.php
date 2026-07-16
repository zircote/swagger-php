<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Generator;
use OpenApi\Serializer;
use OpenApi\Tests\Concerns\AssertsBuilderResult;
use OpenApi\Tests\Concerns\UsesExamples;
use OpenApi\Type\LegacyTypeResolver;
use OpenApi\TypeResolverInterface;
use PHPUnit\Framework\Attributes\DataProvider;

final class ExamplesTest extends OpenApiTestCase
{
    use AssertsBuilderResult;
    use UsesExamples;

    public static function exampleSpecs(): iterable
    {
        $examples = [
            'api',
            'misc',
            'nesting',
            'petstore',
            'polymorphism',
            'using-interfaces',
            'using-links',
            'using-refs',
            'using-traits',
            'webhooks',
        ];
        $implementations = [
            'annotations',
            'attributes',
            'mixed', // classic annotations + attributes
            'spec',
        ];
        $versions = [
            '3.0.0',
            '3.1.0',
            '3.2.0',
        ];
        $modes = [
            Mode::CLASSIC,
            Mode::HYBRID,
            Mode::SPEC,
        ];

        foreach (self::getTypeResolvers() as $resolverName => $typeResolver) {
            foreach ($examples as $example) {
                foreach ($implementations as $implementation) {
                    if (!file_exists(self::examplePath($example) . '/' . $implementation)) {
                        continue;
                    }

                    foreach ($versions as $version) {
                        foreach ($modes as $mode) {
                            if (
                                ($implementation !== 'spec' && $mode === Mode::SPEC)
                                /* @phpstan-ignore function.alreadyNarrowedType */
                                || ($implementation === 'spec' && in_array($mode, [Mode::CLASSIC, Mode::HYBRID, Mode::SPEC], true))
                                || ($typeResolver instanceof LegacyTypeResolver && $mode === Mode::HYBRID)
                            ) {
                                continue;
                            }

                            if (!file_exists(self::getSpecFilename($example, $implementation, $version, $mode))) {
                                continue;
                            }

                            $key = "{$example}:{$resolverName}-{$implementation}-{$mode->value}-{$version}";
                            yield $key => [
                                $typeResolver,
                                $example,
                                $implementation,
                                $version,
                                $mode,
                            ];
                        }
                    }
                }
            }
        }
    }

    /**
     * Validate openapi definitions of the included examples.
     */
    #[DataProvider('exampleSpecs')]
    public function testExample(TypeResolverInterface $typeResolver, string $name, string $implementation, string $version, Mode $mode): void
    {
        $this->registerExampleClassloader($name, $implementation);

        $path = self::examplePath("{$name}/{$implementation}");
        $specFilename = self::getSpecFilename($name, $implementation, $version, $mode);

        $result = (new Builder())
            ->setMode($mode)
            ->addSource($path)
            ->setVersion($version)
            ->setLogger($this->getTrackingLogger())
            ->withGenerator(fn (Generator $generator): Generator => $generator->setTypeResolver($typeResolver))
            ->build();
        // file_put_contents($specFilename, $result->toYaml());
        $this->assertBuilderResult($result);
        $this->assertSpecEquals(
            $result->toYaml(),
            file_get_contents($specFilename),
            "Example: {$name}/{$implementation}/" . basename($specFilename)
        );
    }

    #[DataProvider('exampleSpecs')]
    public function testSerializer(TypeResolverInterface $typeResolver, string $name, string $implementation, string $version, Mode $mode): void
    {
        if ($mode === Mode::SPEC) {
            $this->markTestSkipped('Serializer tests are not supported for spec mode');
        }

        $specFilename = self::getSpecFilename($name, $implementation, $version);

        $reserialized = (new Serializer())->deserializeFile($specFilename)->toYaml();

        $this->assertSpecEquals(file_get_contents($specFilename), $reserialized);
    }
}
