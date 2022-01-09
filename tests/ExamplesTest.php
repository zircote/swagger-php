<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use Composer\Autoload\ClassLoader;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Analysers\TokenAnalyser;
use OpenApi\Annotations\OpenApi;
use OpenApi\Generator;

class ExamplesTest extends OpenApiTestCase
{
    public function exampleMappings()
    {
        $analysers = [
            'token' => new TokenAnalyser(),
            'reflection/docblock' => new ReflectionAnalyser([new DocBlockAnnotationFactory()]),
        ];

        $examples = [
            'example-object' => [OpenApi::VERSION_3_0_0, 'example-object', 'example-object.yaml'],
            'misc' => [OpenApi::VERSION_3_0_0, 'misc', 'misc.yaml'],
            'nesting' => [OpenApi::VERSION_3_0_0, 'nesting', 'nesting.yaml'],
            'openapi-spec' => [OpenApi::VERSION_3_0_0, 'openapi-spec', 'openapi-spec.yaml'],
            'petstore-3.0' => [OpenApi::VERSION_3_0_0, 'petstore-3.0', 'petstore-3.0.yaml'],
            'petstore.swagger.io' => [OpenApi::VERSION_3_0_0, 'petstore.swagger.io', 'petstore.swagger.io.yaml'],
            'swagger-spec/petstore' => [OpenApi::VERSION_3_0_0, 'swagger-spec/petstore', 'petstore.yaml'],
            'swagger-spec/petstore-simple' => [OpenApi::VERSION_3_0_0, 'swagger-spec/petstore-simple', 'petstore-simple.yaml'],
            'swagger-spec/petstore-simple-3.1.0' => [OpenApi::VERSION_3_1_0, 'swagger-spec/petstore-simple', 'petstore-simple-3.1.0.yaml'],
            'swagger-spec/petstore-with-external-docs' => [OpenApi::VERSION_3_0_0, 'swagger-spec/petstore-with-external-docs', 'petstore-with-external-docs.yaml'],
            'using-interfaces' => [OpenApi::VERSION_3_0_0, 'using-interfaces', 'using-interfaces.yaml'],
            'using-refs' => [OpenApi::VERSION_3_0_0, 'using-refs', 'using-refs.yaml'],
            'using-traits' => [OpenApi::VERSION_3_0_0, 'using-traits', 'using-traits.yaml'],
        ];

        foreach ($examples as $ekey => $example) {
            foreach ($analysers as $akey => $analyser) {
                if (\PHP_VERSION_ID < 80100 && 'using-refs' == $ekey) {
                    continue;
                }
                yield $ekey . ':' . $akey => array_merge($example, [$analyser]);
            }
        }

        if (\PHP_VERSION_ID >= 80100) {
            yield 'reflection/attribute:openapi-spec-attributes' => [OpenApi::VERSION_3_0_0, 'openapi-spec-attributes', 'openapi-spec-attributes.yaml', new ReflectionAnalyser([new AttributeAnnotationFactory()])];
        }
    }

    /**
     * Validate openapi definitions of the included examples.
     *
     * @dataProvider exampleMappings
     */
    public function testExamples($version, $example, $spec, $analyser)
    {
        // register autoloader for examples that require autoloading due to inheritance, etc.
        $path = $this->example($example);
        $exampleNS = str_replace(' ', '', ucwords(str_replace(['-', '.'], ' ', $example)));
        $classloader = new ClassLoader();
        $classloader->addPsr4('OpenApi\\Examples\\' . $exampleNS . '\\', $path);
        $classloader->register();

        $path = $this->example($example);
        $openapi = (new Generator())
            ->setVersion($version)
            ->setAnalyser($analyser)
            ->generate([$path], null, true);
        //file_put_contents($path . '/' . $spec, $openapi->toYaml());
        $this->assertSpecEquals(
            $openapi,
            file_get_contents($path . '/' . $spec),
            get_class($analyser) . ': Examples/' . $example . '/' . $spec
        );
    }
}
