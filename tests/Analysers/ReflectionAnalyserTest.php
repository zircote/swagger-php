<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

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

final class ReflectionAnalyserTest extends OpenApiTestCase
{
    protected function collectingAnnotationFactory(): AnnotationFactoryInterface
    {
        return new class () implements AnnotationFactoryInterface {
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
            ExtendsClass::class,
            'extendsClassFunc',
            'extendsClassProp',
        ];
        $this->assertSame($expected, array_keys($annotationFactory->reflectors));
    }

    public function testTraitInheritance(): void
    {
        $analyser = new ReflectionAnalyser([$annotationFactory = $this->collectingAnnotationFactory()]);
        $analyser->fromFqdn(ExtendsTrait::class, new Analysis([], $this->getContext()));

        $expected = [
            ExtendsTrait::class,
            'extendsTraitFunc',
            'extendsTraitProp',
        ];
        $this->assertSame($expected, array_keys($annotationFactory->reflectors));
    }

    public static function analysers(): iterable
    {
        return [
            'docblocks-attributes' => [new ReflectionAnalyser([new DocBlockAnnotationFactory(), new AttributeAnnotationFactory()])],
            'attributes-docblocks' => [new ReflectionAnalyser([new AttributeAnnotationFactory(), new DocBlockAnnotationFactory()])],
        ];
    }

    /**
     * @requires PHP 8.1
     */
    public function testPhp8PromotedProperties(): void
    {
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
        $this->assertStringContainsString('Label List', (string) $labels->_context->comment);
        $this->assertStringContainsString('Tag List', (string) $tags->_context->comment);
        $this->assertEmpty($id->_context->comment);
    }
}
