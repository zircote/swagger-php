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
            'example-object' => ['example-object', 'example-object.yaml'],
            'misc' => ['misc', 'misc.yaml'],
            'nesting' => ['nesting', 'nesting.yaml'],
            'openapi-spec' => ['openapi-spec', 'openapi-spec.yaml'],
            'petstore-3.0' => ['petstore-3.0', 'petstore-3.0.yaml'],
            'petstore.swagger.io' => ['petstore.swagger.io', 'petstore.swagger.io.yaml'],
            'swagger-spec/petstore' => ['swagger-spec/petstore', 'petstore.yaml'],
            'swagger-spec/petstore-simple' => ['swagger-spec/petstore-simple', 'petstore-simple.yaml'],
            'swagger-spec/petstore-with-external-docs' => ['swagger-spec/petstore-with-external-docs', 'petstore-with-external-docs.yaml'],
            'using-interfaces' => ['using-interfaces', 'using-interfaces.yaml'],
            'using-refs' => ['using-refs', 'using-refs.yaml'],
            'using-traits' => ['using-traits', 'using-traits.yaml'],
        ];

        foreach ($examples as $ekey => $example) {
            foreach ($analysers as $akey => $analyser) {
                yield $akey . ':' . $ekey => array_merge($example, [$analyser]);
            }
        }

        if (\PHP_VERSION_ID >= 80100) {
            yield 'reflection/attribute:openapi-spec-attributes' => ['openapi-spec-attributes', 'openapi-spec-attributes.yaml', new ReflectionAnalyser([new AttributeAnnotationFactory()])];
        }
    }

    /**
     * Validate openapi definitions of the included examples.
     *
     * @dataProvider exampleMappings
     */
    public function testExamples($example, $spec, $analyser)
    {
        // register autoloader for examples that require autoloading due to inheritance, etc.
        $path = $this->example($example);
        $exampleNS = str_replace(' ', '', ucwords(str_replace(['-', '.'], ' ', $example)));
        $classloader = new ClassLoader();
        $classloader->addPsr4('OpenApi\\Examples\\' . $exampleNS . '\\', $path);
        $classloader->register();

        $path = $this->example($example);
        $openapi = (new Generator())
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
