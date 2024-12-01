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
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Serializer;

class ExamplesTest extends OpenApiTestCase
{
    public static function exampleDetails(): iterable
    {
        yield 'example-object' => [
            'version' => OA\OpenApi::VERSION_3_0_0,
            'example' => 'example-object',
            'example-object.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        yield 'misc' => [
            'version' => OA\OpenApi::VERSION_3_0_0,
            'example' => 'misc',
            'misc.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        yield 'nesting' => [
            'version' => OA\OpenApi::VERSION_3_0_0,
            'example' => 'nesting',
            'nesting.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        yield 'petstore-3.0' => [
            'version' => OA\OpenApi::VERSION_3_0_0,
            'example' => 'petstore-3.0',
            'petstore-3.0.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        yield 'petstore.swagger.io' => [
            'version' => OA\OpenApi::VERSION_3_0_0,
            'example' => 'petstore.swagger.io',
            'petstore.swagger.io.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        yield 'swagger-spec/petstore' => [
            'version' => OA\OpenApi::VERSION_3_0_0,
            'example' => 'swagger-spec/petstore',
            'petstore.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        yield 'swagger-spec/petstore-simple' => [
            'version' => OA\OpenApi::VERSION_3_0_0,
            'example' => 'swagger-spec/petstore-simple',
            'petstore-simple.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        yield 'swagger-spec/petstore-simple-3.1.0' => [
            'version' => OA\OpenApi::VERSION_3_1_0,
            'example' => 'swagger-spec/petstore-simple',
            'petstore-simple-3.1.0.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        yield 'swagger-spec/petstore-with-external-docs' => [
            'version' => OA\OpenApi::VERSION_3_0_0,
            'example' => 'swagger-spec/petstore-with-external-docs',
            'petstore-with-external-docs.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        yield 'polymorphism' => [
            'version' => OA\OpenApi::VERSION_3_0_0,
            'example' => 'polymorphism',
            'polymorphism.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        yield 'polymorphism-3.1.0' => [
            'version' => OA\OpenApi::VERSION_3_1_0,
            'example' => 'polymorphism',
            'polymorphism-3.1.0.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        yield 'using-interfaces' => [
            'version' => OA\OpenApi::VERSION_3_0_0,
            'example' => 'using-interfaces',
            'using-interfaces.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        yield 'using-traits' => [
            'version' => OA\OpenApi::VERSION_3_0_0,
            'example' => 'using-traits',
            'using-traits.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        yield 'using-links' => [
            'version' => OA\OpenApi::VERSION_3_0_0,
            'example' => 'using-links',
            'using-links.yaml',
            'debug' => false,
            'expectedLog' => [],
            'analysers' => ['reflection'],
        ];

        if (\PHP_VERSION_ID >= 80100) {
            yield 'using-refs' => [
                'version' => OA\OpenApi::VERSION_3_0_0,
                'example' => 'using-refs',
                'using-refs.yaml',
                'debug' => false,
                'expectedLog' => [],
                'analysers' => ['reflection'],
            ];

            yield 'webhooks' => [
                'version' => OA\OpenApi::VERSION_3_1_0,
                'example' => 'webhooks',
                'webhooks.yaml',
                'debug' => false,
                'expectedLog' => [],
                'analysers' => ['reflection'],
            ];

            yield 'webhooks81' => [
                'version' => OA\OpenApi::VERSION_3_1_0,
                'example' => 'webhooks81',
                'webhooks.yaml',
                'debug' => false,
                'expectedLog' => [],
                'analysers' => ['reflection'],
            ];

            yield 'using-links-php81' => [
                'version' => OA\OpenApi::VERSION_3_0_0,
                'example' => 'using-links-php81',
                'using-links-php81.yaml',
                'debug' => true,
                'expectedLog' => ['JetBrains\PhpStorm\ArrayShape'],
                'analysers' => ['reflection'],
            ];
        }
    }

    public static function exampleMappings(): iterable
    {
        $analysers = [
            'reflection' => new ReflectionAnalyser([new DocBlockAnnotationFactory(), new AttributeAnnotationFactory()]),
        ];

        foreach (static::exampleDetails() as $exampleKey => $example) {
            $exampleAnalysers = $example['analysers'];
            unset($example['analysers']);
            foreach ($exampleAnalysers as $analyserKey) {
                yield $exampleKey . ':' . $analyserKey => array_merge($example, [$analysers[$analyserKey]]);
            }
        }
    }

    /**
     * Validate openapi definitions of the included examples.
     *
     * @dataProvider exampleMappings
     */
    public function testExamples(string $version, string $example, string $spec, bool $debug, array $expectedLog, AnalyserInterface $analyser): void
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
        $openapi = (new Generator($this->getTrackingLogger($debug)))
            ->setVersion($version)
            ->setAnalyser($analyser)
            ->generate([$path]);
        // file_put_contents($path . '/' . $spec, $openapi->toYaml());
        $this->assertSpecEquals(
            $openapi,
            file_get_contents($path . '/' . $spec),
            get_class($analyser) . ': Examples/' . $example . '/' . $spec
        );
    }

    /**
     * @dataProvider exampleDetails
     */
    public function testSerializer(string $version, string $example, string $spec, bool $debug, array $expectedLog): void
    {
        $filename = $this->example($example) . '/' . $spec;
        $reserialized = (new Serializer())->deserializeFile($filename)->toYaml();

        $this->assertEquals(file_get_contents($filename), $reserialized);
    }
}
