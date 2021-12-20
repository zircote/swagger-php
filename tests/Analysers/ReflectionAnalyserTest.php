<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysers\AnnotationFactoryInterface;
use OpenApi\Analysis;
use OpenApi\Annotations\Get;
use OpenApi\Annotations\Operation;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Schema;
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
        $analysis = (new Generator())
            ->withContext(function (Generator $generator) use ($analyser) {
                $analyser->setGenerator($generator);
                $analysis = $analyser->fromFile($this->fixture('Apis/DocBlocks/basic.php'), $this->getContext());
                $analysis->process($generator->getProcessors());

                return $analysis;
            });

        $operations = $analysis->getAnnotationsOfType(Operation::class);
        $this->assertIsArray($operations);

        $spec = $this->fixture('Apis/basic.yaml');
        //file_put_contents($spec, $analysis->openapi->toYaml());
        $this->assertTrue($analysis->validate());
        $this->assertSpecEquals($analysis->openapi, file_get_contents($spec));
    }

    /**
     * @dataProvider analysers
     * @requires PHP 8.1
     */
    public function testApiAttributesBasic(AnalyserInterface $analyser)
    {
        /** @var Analysis $analysis */
        $analysis = (new Generator())
            ->addAlias('oaf', 'OpenApi\\Tests\\Annotations')
            ->addNamespace('OpenApi\\Tests\\Annotations\\')
            ->withContext(function (Generator $generator) use ($analyser) {
                $analyser->setGenerator($generator);
                $analysis = $analyser->fromFile($this->fixture('Apis/Attributes/basic.php'), $this->getContext());
                $analysis->process((new Generator())->getProcessors());

                return $analysis;
            });

        $operations = $analysis->getAnnotationsOfType(Operation::class);
        $this->assertIsArray($operations);

        $spec = $this->fixture('Apis/basic.yaml');
        file_put_contents($spec, $analysis->openapi->toYaml());
        $this->assertTrue($analysis->validate());
        $this->assertSpecEquals($analysis->openapi, file_get_contents($spec));

        // check CustomAttachable is only attached to @OA\Get
        /** @var Get[] $gets */
        $gets = $analysis->getAnnotationsOfType(Get::class, true);
        $this->assertCount(1, $gets);
        $this->assertTrue(is_array($gets[0]->attachables), 'Attachables not set');
        $this->assertCount(1, $gets[0]->attachables);

        /** @var Response[] $responses */
        $responses = $analysis->getAnnotationsOfType(Response::class, true);
        foreach ($responses as $response) {
            $this->assertEquals(Generator::UNDEFINED, $response->attachables);
        }
    }

    /**
     * @dataProvider analysers
     * @requires PHP 8.1
     */
    public function testApiMixedBasic(AnalyserInterface $analyser)
    {
        $analysis = (new Generator())
            ->withContext(function (Generator $generator) use ($analyser) {
                $analyser->setGenerator($generator);
                $analysis = $analyser->fromFile($this->fixture('Apis/Mixed/basic.php'), $this->getContext());
                $analysis->process((new Generator())->getProcessors());

                return $analysis;
            });

        $operations = $analysis->getAnnotationsOfType(Operation::class);
        $this->assertIsArray($operations);

        $spec = $this->fixture('Apis/basic.yaml');
        //file_put_contents($spec, $analysis->openapi->toYaml());
        $this->assertTrue($analysis->validate());
        $this->assertSpecEquals($analysis->openapi, file_get_contents($spec));
    }


    /**
     * @dataProvider analysers
     * @requires PHP 8.0
     */
    public function testPhp8NamedArguments(AnalyserInterface $analyser)
    {
        $analysis = (new Generator())
            ->withContext(function (Generator $generator) use ($analyser) {
                $analyser->setGenerator($generator);
                $analysis = $analyser->fromFile($this->fixture('PHP/Php8NamedArguments.php'), $this->getContext());

                return $analysis;
            });
        $this->assertCount(2, $analysis->annotations);

        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);
        $this->assertCount(1, $schemas);
    }
}
