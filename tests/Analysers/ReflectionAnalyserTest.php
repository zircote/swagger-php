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
use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\Processors\CleanUnusedComponents;
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

            public function isSupported(): bool
            {
                return true;
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

    public static function analysers(): iterable
    {
        return [
            'docblocks-attributes' => [new ReflectionAnalyser([new DocBlockAnnotationFactory(), new AttributeAnnotationFactory()])],
            'attributes-docblocks' => [new ReflectionAnalyser([new AttributeAnnotationFactory(), new DocBlockAnnotationFactory()])],
        ];
    }

    /**
     * @dataProvider analysers
     *
     * @requires     PHP 8.1
     */
    public function testApiDocBlockBasic(AnalyserInterface $analyser): void
    {
        require_once $this->fixture('Apis/DocBlocks/basic.php');

        $analysis = (new Generator())
            ->withContext(function (Generator $generator) use ($analyser) {
                $analyser->setGenerator($generator);
                $analysis = $analyser->fromFile($this->fixture('Apis/DocBlocks/basic.php'), $this->getContext([], $generator->getVersion()));
                $generator->getProcessorPipeline()->process($analysis);

                return $analysis;
            });

        $operations = $analysis->getAnnotationsOfType(OA\Operation::class);
        $this->assertIsArray($operations);

        $spec = $this->fixture('Apis/basic.yaml');
        // file_put_contents($spec, $analysis->openapi->toYaml());
        $this->assertTrue($analysis->validate());
        $this->assertSpecEquals($analysis->openapi, file_get_contents($spec));
    }

    /**
     * @dataProvider analysers
     *
     * @requires     PHP 8.1
     */
    public function testApiAttributesBasic(AnalyserInterface $analyser): void
    {
        require_once $this->fixture('Apis/Attributes/basic.php');

        /** @var Analysis $analysis */
        $analysis = (new Generator())
            ->addAlias('oaf', 'OpenApi\\Tests\\Annotations')
            ->addNamespace('OpenApi\\Tests\\Annotations\\')
            ->withContext(function (Generator $generator) use ($analyser) {
                $analyser->setGenerator($generator);
                $analysis = $analyser->fromFile($this->fixture('Apis/Attributes/basic.php'), $this->getContext([], $generator->getVersion()));
                $generator->getProcessorPipeline()->process($analysis);

                return $analysis;
            });

        $operations = $analysis->getAnnotationsOfType(OA\Operation::class);
        $this->assertIsArray($operations);

        $spec = $this->fixture('Apis/basic.yaml');
        // file_put_contents($spec, $analysis->openapi->toYaml());
        $this->assertTrue($analysis->validate());
        $this->assertSpecEquals($analysis->openapi, file_get_contents($spec));

        // check CustomAttachable is only attached to @OA\Get
        /** @var OA\Get[] $gets */
        $gets = $analysis->getAnnotationsOfType(OA\Get::class, true);
        $this->assertCount(2, $gets);
        $this->assertTrue(is_array($gets[0]->attachables), 'Attachables not set');
        $this->assertCount(1, $gets[0]->attachables);

        /** @var OA\Response[] $responses */
        $responses = $analysis->getAnnotationsOfType(OA\Response::class, true);
        foreach ($responses as $response) {
            $this->assertEquals(Generator::UNDEFINED, $response->attachables);
        }
    }

    /**
     * @dataProvider analysers
     *
     * @requires     PHP 8.1
     */
    public function testApiMixedBasic(AnalyserInterface $analyser): void
    {
        require_once $this->fixture('Apis/Mixed/basic.php');

        $analysis = (new Generator())
            ->withContext(function (Generator $generator) use ($analyser) {
                $analyser->setGenerator($generator);
                $analysis = $analyser->fromFile($this->fixture('Apis/Mixed/basic.php'), $this->getContext([], $generator->getVersion()));
                $generator->getProcessorPipeline()->process($analysis);

                return $analysis;
            });

        $operations = $analysis->getAnnotationsOfType(OA\Operation::class);
        $this->assertIsArray($operations);

        $spec = $this->fixture('Apis/basic.yaml');
        // file_put_contents($spec, $analysis->openapi->toYaml());
        $this->assertTrue($analysis->validate());
        $this->assertSpecEquals($analysis->openapi, file_get_contents($spec));
    }

    /**
     * @requires PHP 8.1
     */
    public function testPhp8PromotedProperties(): void
    {
        $this->skipLegacy();

        $analysis = $this->analysisFromFixtures(['PHP/Php8PromotedProperties.php']);
        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class, true);

        $this->assertCount(1, $schemas);
        $analysis->process($this->processors([CleanUnusedComponents::class]));

        /** @var OA\Property[] $properties */
        $properties = $analysis->getAnnotationsOfType(OA\Property::class);

        [$tags, $id, $labels] = $properties;

        $this->assertCount(3, $properties);
        $this->assertEquals('tags', $tags->property);
        $this->assertEquals('id', $id->property);
        $this->assertEquals('labels', $labels->property);

        // regression: check doc blocks
        $this->assertStringContainsString('Label List', $labels->_context->comment);
        $this->assertStringContainsString('Tag List', $tags->_context->comment);
        $this->assertEmpty($id->_context->comment);
    }
}
