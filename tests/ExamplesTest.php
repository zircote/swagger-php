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
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Serializer;

class ExamplesTest extends OpenApiTestCase
{
    public function exampleDetails(): iterable
    {
        yield 'example-object' => [
            OA\OpenApi::VERSION_3_0_0,
            'example-object',
            'example-object.yaml',
            false,
            [],
        ];

        yield 'misc' => [
            OA\OpenApi::VERSION_3_0_0,
            'misc',
            'misc.yaml',
            false,
            [],
        ];

        yield 'nesting' => [
            OA\OpenApi::VERSION_3_0_0,
            'nesting',
            'nesting.yaml',
            false,
            [],
        ];

        yield 'petstore-3.0' => [
            OA\OpenApi::VERSION_3_0_0,
            'petstore-3.0',
            'petstore-3.0.yaml',
            false,
            [],
        ];

        yield 'petstore.swagger.io' => [
            OA\OpenApi::VERSION_3_0_0,
            'petstore.swagger.io',
            'petstore.swagger.io.yaml',
            false,
            [],
        ];

        yield 'swagger-spec/petstore' => [
            OA\OpenApi::VERSION_3_0_0,
            'swagger-spec/petstore',
            'petstore.yaml',
            false,
            [],
        ];

        yield 'swagger-spec/petstore-simple' => [
            OA\OpenApi::VERSION_3_0_0,
            'swagger-spec/petstore-simple',
            'petstore-simple.yaml',
            false,
            [],
        ];

        yield 'swagger-spec/petstore-simple-3.1.0' => [
            OA\OpenApi::VERSION_3_1_0,
            'swagger-spec/petstore-simple',
            'petstore-simple-3.1.0.yaml',
            false,
            [],
        ];

        yield 'swagger-spec/petstore-with-external-docs' => [
            OA\OpenApi::VERSION_3_0_0,
            'swagger-spec/petstore-with-external-docs',
            'petstore-with-external-docs.yaml',
            false,
            [],
        ];

        yield 'polymorphism' => [
            OA\OpenApi::VERSION_3_0_0,
            'polymorphism',
            'polymorphism.yaml',
            false,
            [],
        ];

        yield 'polymorphism-3.1.0' => [
            OA\OpenApi::VERSION_3_1_0,
            'polymorphism',
            'polymorphism-3.1.0.yaml',
            false,
            [],
        ];

        yield 'using-interfaces' => [
            OA\OpenApi::VERSION_3_0_0,
            'using-interfaces',
            'using-interfaces.yaml',
            false,
            [],
        ];

        yield 'using-refs' => [
            OA\OpenApi::VERSION_3_0_0,
            'using-refs',
            'using-refs.yaml',
            false,
            [],
        ];

        yield 'using-traits' => [
            OA\OpenApi::VERSION_3_0_0,
            'using-traits',
            'using-traits.yaml',
            false,
            [],
        ];

        yield 'using-links' => [
            OA\OpenApi::VERSION_3_0_0,
            'using-links',
            'using-links.yaml',
            false,
            [],
        ];

        if (\PHP_VERSION_ID >= 80100) {
            yield 'webhooks' => [
                OA\OpenApi::VERSION_3_1_0,
                'webhooks',
                'webhooks.yaml',
                false,
                [],
            ];

            yield 'using-links-php81' => [
                OA\OpenApi::VERSION_3_0_0,
                'using-links-php81',
                'using-links-php81.yaml',
                true,
                ['JetBrains\PhpStorm\ArrayShape'],
            ];
        }
    }

    public function exampleMappings(): iterable
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
         file_put_contents($path . '/' . $spec, $openapi->toYaml());
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
