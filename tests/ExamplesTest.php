<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use Composer\Autoload\ClassLoader;
use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Analysers\TokenAnalyser;
use OpenApi\Annotations\OpenApi;
use OpenApi\Generator;
use OpenApi\Serializer;

class ExamplesTest extends OpenApiTestCase
{
    public function exampleDetails()
    {
        yield 'example-object' => [
            OpenApi::VERSION_3_0_0,
            'example-object',
            'example-object.yaml',
            [],
        ];

        yield 'misc' => [
            OpenApi::VERSION_3_0_0,
            'misc',
            'misc.yaml',
            [],
        ];

        yield 'nesting' => [
            OpenApi::VERSION_3_0_0,
            'nesting',
            'nesting.yaml',
            [],
        ];

        yield 'petstore-3.0' => [
            OpenApi::VERSION_3_0_0,
            'petstore-3.0',
            'petstore-3.0.yaml',
            [],
        ];

        yield 'petstore.swagger.io' => [
            OpenApi::VERSION_3_0_0,
            'petstore.swagger.io',
            'petstore.swagger.io.yaml',
            [],
        ];

        yield 'swagger-spec/petstore' => [
            OpenApi::VERSION_3_0_0,
            'swagger-spec/petstore',
            'petstore.yaml',
            [],
        ];

        yield 'swagger-spec/petstore-simple' => [
            OpenApi::VERSION_3_0_0,
            'swagger-spec/petstore-simple',
            'petstore-simple.yaml',
            [],
        ];

        yield 'swagger-spec/petstore-simple-3.1.0' => [
            OpenApi::VERSION_3_1_0,
            'swagger-spec/petstore-simple',
            'petstore-simple-3.1.0.yaml',
            [],
        ];

        yield 'swagger-spec/petstore-with-external-docs' => [
            OpenApi::VERSION_3_0_0,
            'swagger-spec/petstore-with-external-docs',
            'petstore-with-external-docs.yaml',
            [],
        ];

        yield 'polymorphism' => [
            OpenApi::VERSION_3_0_0,
            'polymorphism',
            'polymorphism.yaml',
            [],
        ];

        yield 'polymorphism-3.1.0' => [
            OpenApi::VERSION_3_1_0,
            'polymorphism',
            'polymorphism-3.1.0.yaml',
            [],
        ];

        yield 'using-interfaces' => [
            OpenApi::VERSION_3_0_0,
            'using-interfaces',
            'using-interfaces.yaml',
            [],
        ];

        yield 'using-refs' => [
            OpenApi::VERSION_3_0_0,
            'using-refs',
            'using-refs.yaml',
            [],
        ];

        yield 'using-traits' => [
            OpenApi::VERSION_3_0_0,
            'using-traits',
            'using-traits.yaml',
            [],
        ];

        yield 'using-links' => [
            OpenApi::VERSION_3_0_0,
            'using-links',
            'using-links.yaml',
            [],
        ];

        if (\PHP_VERSION_ID >= 80100) {
            yield 'using-links-php81' => [
                OpenApi::VERSION_3_0_0,
                'using-links-php81',
                'using-links-php81.yaml',
                ['JetBrains\PhpStorm\ArrayShape'],
            ];
        }
    }

    public function exampleMappings()
    {
        $analysers = [
            'token' => new TokenAnalyser(),
            'reflection' => new ReflectionAnalyser([new DocBlockAnnotationFactory(), new AttributeAnnotationFactory()]),
        ];

        foreach ($this->exampleDetails() as $eKey => $example) {
            foreach ($analysers as $aKey => $analyser) {
                if (0 === strpos($eKey, 'polymorphism') && 'token' == $aKey) {
                    continue;
                }
                if ((\PHP_VERSION_ID < 80100 || 'token' == $aKey) && 'using-refs' == $eKey) {
                    continue;
                }
                if ('using-links-php81' == $eKey && 'token' == $aKey) {
                    continue;
                }
                yield $eKey . ':' . $aKey => array_merge($example, [$analyser]);
            }
        }
    }

    /**
     * Validate openapi definitions of the included examples.
     *
     * @dataProvider exampleMappings
     */
    public function testExamples(string $version, string $example, string $spec, array $expectedLog, AnalyserInterface $analyser): void
    {
        // register autoloader for examples that require autoloading due to inheritance, etc.
        $path = $this->example($example);
        $exampleNS = str_replace(' ', '', ucwords(str_replace(['-', '.'], ' ', $example)));
        $exampleNS = str_replace(' ', '\\', ucwords(str_replace('/', ' ', $exampleNS)));
        $classloader = new ClassLoader();
        $classloader->addPsr4('OpenApi\\Examples\\' . $exampleNS . '\\', $path);
        $classloader->register();

        foreach ($expectedLog as $logLine) {
            $this->assertOpenApiLogEntryContains($logLine);
        }

        $path = $this->example($example);
        $openapi = (new Generator($this->getTrackingLogger()))
            ->setVersion($version)
            ->setAnalyser($analyser)
            ->generate([$path]);
        //file_put_contents($path . '/' . $spec, $openapi->toYaml());
        $this->assertSpecEquals(
            $openapi,
            file_get_contents($path . '/' . $spec),
            get_class($analyser) . ': Examples/' . $example . '/' . $spec
        );
    }

    /**
     * @dataProvider exampleDetails
     */
    public function testSerializer(string $version, string $example, string $spec, array $expectedLog): void
    {
        $filename = $this->example($example) . '/' . $spec;
        $reserialized = (new Serializer())->deserializeFile($filename)->toYaml();

        $this->assertEquals(file_get_contents($filename), $reserialized);
    }
}
