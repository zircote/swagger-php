<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysers\AnnotationFactoryInterface;
use OpenApi\Analysis;
use OpenApi\Annotations\Operation;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\Tests\Fixtures\PHP\Inheritance\ExtendsClass;
use OpenApi\Tests\Fixtures\PHP\Inheritance\ExtendsTrait;
use OpenApi\Tests\OpenApiTestCase;

class ReflectionAnalyserTest extends OpenApiTestCase
{
    protected function collectingAnnotationFactory(): AnnotationFactoryInterface
    {
        return new class() implements AnnotationFactoryInterface {
            public $reflectors = [];

            public function build(\Reflector $reflector, Context $context): array
            {
                $this->reflectors[$reflector->name] = $reflector;

                return [];
            }

            public function setGenerator(Generator $generator): void
            {
                // noop
            }
        };
    }

    public function testClassInheritance()
    {
        $analyser = new ReflectionAnalyser([$annotationFactory = $this->collectingAnnotationFactory()]);
        $analyser->fromFqdn(ExtendsClass::class, new Analysis([], $this->getContext()));

        $expected = [
            'OpenApi\Tests\Fixtures\PHP\Inheritance\ExtendsClass',
            'extendsClassFunc',
            'extendsClassProp',
        ];
        $this->assertEquals($expected, array_keys($annotationFactory->reflectors));
    }

    public function testTraitInheritance()
    {
        $analyser = new ReflectionAnalyser([$annotationFactory = $this->collectingAnnotationFactory()]);
        $analyser->fromFqdn(ExtendsTrait::class, new Analysis([], $this->getContext()));

        $expected = [
            'OpenApi\Tests\Fixtures\PHP\Inheritance\ExtendsTrait',
            'extendsTraitFunc',
            'extendsTraitProp',
        ];
        $this->assertEquals($expected, array_keys($annotationFactory->reflectors));
    }

    public function analysers()
    {
        return [
            'docblocks-attributes' => [new ReflectionAnalyser([new DocBlockAnnotationFactory(), new AttributeAnnotationFactory()])],
            'attributes-docblocks' => [new ReflectionAnalyser([new AttributeAnnotationFactory(),new DocBlockAnnotationFactory()])],
        ];
    }

    /**
     * @dataProvider analysers
     */
    public function testApiDocBlockBasic(AnalyserInterface $analyser)
    {
        $fixture = $this->fixture('Apis/DocBlocks/basic.php');
        //require_once $fixture;

        $analysis = $analyser->fromFile($fixture, $this->getContext());
        $analysis->process((new Generator())->getProcessors());

        $operations = $analysis->getAnnotationsOfType(Operation::class);
        $this->assertIsArray($operations);

        $spec = $this->fixture('Apis/basic.yaml');
        //file_put_contents($spec, $analysis->openapi->toYaml());
        $this->assertTrue($analysis->validate());
        $this->assertSpecEquals(file_get_contents($spec), $analysis->openapi);
    }

    /**
     * @dataProvider analysers
     * @requires PHP 8.1
     */
    public function testApiAttributesBasic(AnalyserInterface $analyser)
    {
        $fixture = $this->fixture('Apis/Attributes/basic.php');
        require_once $fixture;

        $analysis = $analyser->fromFile($fixture, $this->getContext());
        $analysis->process((new Generator())->getProcessors());

        $operations = $analysis->getAnnotationsOfType(Operation::class);
        $this->assertIsArray($operations);

        $spec = $this->fixture('Apis/basic.yaml');
        //file_put_contents($spec, $analysis->openapi->toYaml());
        $this->assertTrue($analysis->validate());
        $this->assertSpecEquals(file_get_contents($spec), $analysis->openapi);
    }

    /**
     * @dataProvider analysers
     * @requires PHP 8.1
     */
    public function testApiMixedBasic(AnalyserInterface $analyser)
    {
        $fixture = $this->fixture('Apis/Mixed/basic.php');
        require_once $fixture;

        $analysis = $analyser->fromFile($fixture, $this->getContext());
        $analysis->process((new Generator())->getProcessors());

        $operations = $analysis->getAnnotationsOfType(Operation::class);
        $this->assertIsArray($operations);

        $spec = $this->fixture('Apis/basic.yaml');
        //file_put_contents($spec, $analysis->openapi->toYaml());
        $this->assertTrue($analysis->validate());
        $this->assertSpecEquals(file_get_contents($spec), $analysis->openapi);
    }
}
