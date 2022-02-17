<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysers\AnnotationFactoryInterface;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Analysers\TokenAnalyser;
use OpenApi\Analysis;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Operation;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Schema;
use OpenApi\Attributes\Get;
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

    public function testClassInheritance(): void
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

    public function testTraitInheritance(): void
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
            'attributes-docblocks' => [new ReflectionAnalyser([new AttributeAnnotationFactory(), new DocBlockAnnotationFactory()])],
        ];
    }

    /**
     * @dataProvider analysers
     * @requires     PHP 8.1
     */
    public function testApiDocBlockBasic(AnalyserInterface $analyser): void
    {
        require_once $this->fixture('Apis/DocBlocks/basic.php');

        $analysis = (new Generator())
            ->setVersion(OpenApi::VERSION_3_1_0)
            ->withContext(function (Generator $generator) use ($analyser) {
                $analyser->setGenerator($generator);
                $analysis = $analyser->fromFile($this->fixture('Apis/DocBlocks/basic.php'), $this->getContext([], $generator->getVersion()));
                $analysis->process($generator->getProcessors());
                $analysis->openapi->openapi = $generator->getVersion();

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
     * @requires     PHP 8.1
     */
    public function testApiAttributesBasic(AnalyserInterface $analyser): void
    {
        require_once $this->fixture('Apis/Attributes/basic.php');

        /** @var Analysis $analysis */
        $analysis = (new Generator())
            ->setVersion(OpenApi::VERSION_3_1_0)
            ->addAlias('oaf', 'OpenApi\\Tests\\Annotations')
            ->addNamespace('OpenApi\\Tests\\Annotations\\')
            ->withContext(function (Generator $generator) use ($analyser) {
                $analyser->setGenerator($generator);
                $analysis = $analyser->fromFile($this->fixture('Apis/Attributes/basic.php'), $this->getContext([], $generator->getVersion()));
                $analysis->process((new Generator())->getProcessors());
                $analysis->openapi->openapi = $generator->getVersion();

                return $analysis;
            });

        $operations = $analysis->getAnnotationsOfType(Operation::class);
        $this->assertIsArray($operations);

        $spec = $this->fixture('Apis/basic.yaml');
        //file_put_contents($spec, $analysis->openapi->toYaml());
        $this->assertTrue($analysis->validate());
        $this->assertSpecEquals($analysis->openapi, file_get_contents($spec));

        // check CustomAttachable is only attached to @OA\Get
        /** @var Get[] $gets */
        $gets = $analysis->getAnnotationsOfType(Get::class, true);
        $this->assertCount(2, $gets);
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
     * @requires     PHP 8.1
     */
    public function testApiMixedBasic(AnalyserInterface $analyser): void
    {
        require_once $this->fixture('Apis/Mixed/basic.php');

        $analysis = (new Generator())
            ->setVersion(OpenApi::VERSION_3_1_0)
            ->withContext(function (Generator $generator) use ($analyser) {
                $analyser->setGenerator($generator);
                $analysis = $analyser->fromFile($this->fixture('Apis/Mixed/basic.php'), $this->getContext([], $generator->getVersion()));
                $analysis->process((new Generator())->getProcessors());
                $analysis->openapi->openapi = $generator->getVersion();

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
     * @requires PHP 8.1
     */
    public function testPhp8PromotedProperties(): void
    {
        if ($this->getAnalyzer() instanceof TokenAnalyser) {
            $this->markTestSkipped();
        }

        $analysis = $this->analysisFromFixtures(['PHP/Php8PromotedProperties.php']);
        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);

        $this->assertCount(1, $schemas);
        $analysis->process((new Generator())->getProcessors());

        /** @var Property[] $properties */
        $properties = $analysis->getAnnotationsOfType(Property::class);
        $this->assertCount(2, $properties);
        $this->assertEquals('id', $properties[0]->property);
        $this->assertEquals('labels', $properties[1]->property);
    }
}
