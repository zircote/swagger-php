<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Annotations as OA;
use OpenApi\Builder;
use OpenApi\Generator;
use OpenApi\Serializer;
use OpenApi\Tests\Concerns\UsesExamples;
use OpenApi\TypeResolverInterface;
use PHPUnit\Framework\Attributes\DataProvider;

final class ExamplesTest extends OpenApiTestCase
{
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
        $implementations = ['annotations', 'attributes', 'mixed', 'spec'];
        $versions = [
            OA\OpenApi::VERSION_3_0_0,
            OA\OpenApi::VERSION_3_1_0,
            OA\OpenApi::VERSION_3_2_0,
        ];

        foreach (self::getTypeResolvers() as $resolverName => $typeResolver) {
            foreach ($examples as $example) {
                foreach ($implementations as $implementation) {
                    if (!file_exists(self::examplePath($example) . '/' . $implementation)) {
                        continue;
                    }

                    $modes = $implementation === 'spec' ? ['spec'] : ['classic', 'hybrid'];

                    foreach ($modes as $mode) {
                        foreach ($versions as $version) {
                            if (!file_exists(self::getSpecFilename($example, $implementation, $version))) {
                                continue;
                            }

                            yield "{$example}:{$resolverName}-{$implementation}-{$mode}-{$version}" => [
                                $typeResolver,
                                $example,
                                $implementation,
                                $mode,
                                $version,
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
    public function testExample(TypeResolverInterface $typeResolver, string $name, string $implementation, string $mode, string $version): void
    {
        $this->registerExampleClassloader($name, $implementation);

        $path = self::examplePath("{$name}/{$implementation}");
        $specFilename = self::getSpecFilename($name, $implementation, $version);

        $result = (new Builder())
            ->setMode($mode)
            ->addSource($path)
            ->setVersion($version)
            ->setLogger($this->getTrackingLogger())
            ->withGenerator(fn (Generator $generator): Generator => $generator->setTypeResolver($typeResolver))
            ->build();
        // file_put_contents($specFilename, $result->toYaml());
        $this->assertSpecEquals(
            $result->toYaml(),
            file_get_contents($specFilename),
            "Example: {$name}/{$implementation}/{$mode}/" . basename($specFilename)
        );
    }

    #[DataProvider('exampleSpecs')]
    public function testSerializer(TypeResolverInterface $typeResolver, string $name, string $implementation, string $mode, string $version): void
    {
        if ($mode !== 'classic') {
            $this->markTestSkipped('Serializer test only applies to classic mode');
        }

        $specFilename = self::getSpecFilename($name, $implementation, $version);

        $reserialized = (new Serializer())->deserializeFile($specFilename)->toYaml();

        $this->assertSpecEquals(file_get_contents($specFilename), $reserialized);
    }
}
