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

<<<<<<< HEAD
/**
 * @requires PHP 8.1
 */
class ExamplesTest extends OpenApiTestCase
=======
final class ExamplesTest extends OpenApiTestCase
>>>>>>> 09b3543 (Subject examples and tests to rector rules (#1942))
{
    use UsesExamples;

    public function exampleSpecs(): iterable
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
        $versions = [
            OA\OpenApi::VERSION_3_0_0,
            OA\OpenApi::VERSION_3_1_0,
            OA\OpenApi::VERSION_3_2_0,
        ];

        foreach (self::getTypeResolvers() as $resolverName => $typeResolver) {
            foreach ($examples as $example) {
                foreach ($implementations as $implementation) {
<<<<<<< HEAD
                    if (!file_exists($this->examplePath($example) . '/' . $implementation)) {
=======
                    if (!file_exists(self::examplePath($example) . '/' . $implementation)) {
>>>>>>> 09b3543 (Subject examples and tests to rector rules (#1942))
                        continue;
                    }

                    foreach ($versions as $version) {
<<<<<<< HEAD
                        if (!file_exists($this->getSpecFilename($example, $implementation, $version))) {
=======
                        if (!file_exists(self::getSpecFilename($example, $implementation, $version))) {
>>>>>>> 09b3543 (Subject examples and tests to rector rules (#1942))
                            continue;
                        }

                        yield "{$example}:{$resolverName}-{$implementation}-{$version}" => [
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
     *
     * @dataProvider exampleSpecs
     */
    public function testExample(TypeResolverInterface $typeResolver, string $name, string $implementation, string $version): void
    {
        $this->registerExampleClassloader($name, $implementation);

<<<<<<< HEAD
        $path = $this->examplePath("$name/$implementation");
        $specFilename = $this->getSpecFilename($name, $implementation, $version);
=======
        $path = self::examplePath("{$name}/{$implementation}");
        $specFilename = self::getSpecFilename($name, $implementation, $version);
>>>>>>> 09b3543 (Subject examples and tests to rector rules (#1942))

        $openapi = (new Generator($this->getTrackingLogger()))
            ->setVersion($version)
            ->setTypeResolver($typeResolver)
            ->generate([$path]);
        // file_put_contents($specFilename, $openapi->toYaml());
        $this->assertSpecEquals(
            $openapi,
            file_get_contents($specFilename),
            "Example: {$name}/{$implementation}/" . basename($specFilename)
        );
    }

    /**
     * @dataProvider exampleSpecs
     */
    public function testSerializer(TypeResolverInterface $typeResolver, string $name, string $implementation, string $version): void
    {
<<<<<<< HEAD
        $specFilename = $this->getSpecFilename($name, $implementation, $version);
=======
        $specFilename = self::getSpecFilename($name, $implementation, $version);
>>>>>>> 09b3543 (Subject examples and tests to rector rules (#1942))

        $reserialized = (new Serializer())->deserializeFile($specFilename)->toYaml();

        $this->assertEquals(file_get_contents($specFilename), $reserialized);
    }
}
