<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Tests\Concerns\UsesExamples;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Serializer;
use OpenApi\TypeResolverInterface;
use PHPUnit\Framework\Attributes\DataProvider;

class ExamplesTest extends OpenApiTestCase
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
        $implementations = ['annotations', 'attributes', 'mixed'];
        $versions = [OA\OpenApi::VERSION_3_0_0, OA\OpenApi::VERSION_3_1_0];

        foreach (static::getTypeResolvers() as $resolverName => $typeResolver) {
            foreach ($examples as $example) {
                foreach ($implementations as $implementation) {
                    if (!file_exists(static::examplePath($example) . '/' . $implementation)) {
                        continue;
                    }

                    foreach ($versions as $version) {
                        if (!file_exists(static::getSpecFilename($example, $implementation, $version))) {
                            continue;
                        }

                        yield "$example:$resolverName-$implementation-$version" => [
                            $typeResolver,
                            $example,
                            $implementation,
                            $version,
                        ];
                    }
                }
            }
        }
    }

    /**
     * Validate openapi definitions of the included examples.
     */
    #[DataProvider('exampleSpecs')]
    public function testExample(TypeResolverInterface $typeResolver, string $name, string $implementation, string $version): void
    {
        $this->registerExampleClassloader($name, $implementation);

        $path = static::examplePath("$name/$implementation");
        $specFilename = static::getSpecFilename($name, $implementation, $version);

        $openapi = (new Generator($this->getTrackingLogger()))
            ->setVersion($version)
            ->setTypeResolver($typeResolver)
            ->generate([$path]);
        // file_put_contents($specFilename, $openapi->toYaml());
        $this->assertSpecEquals(
            $openapi,
            file_get_contents($specFilename),
            "Example: $name/$implementation/" . basename($specFilename)
        );
    }

    #[DataProvider('exampleSpecs')]
    public function testSerializer(TypeResolverInterface $typeResolver, string $name, string $implementation, string $version): void
    {
        $specFilename = static::getSpecFilename($name, $implementation, $version);

        $reserialized = (new Serializer())->deserializeFile($specFilename)->toYaml();

        $this->assertEquals(file_get_contents($specFilename), $reserialized);
    }
}
