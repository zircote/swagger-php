<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Augmenter;
use OpenApi\Augmenter\OperationIds;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Examples\Specs\Webhooks\Attributes as WebhooksAttributes;
use OpenApi\Examples\Specs\Webhooks\Spec as WebhooksSpec;
use OpenApi\Generator;
use OpenApi\Loggers\CollectingLogger;
use OpenApi\Tests\Concerns\UsesExamples;
use OpenApi\Utils\Pipeline;
use OpenApi\Utils\SourceFinder;

final class BuilderTest extends OpenApiTestCase
{
    use UsesExamples;

    public function testBuild(): void
    {
        $this->registerExampleClassloader('petstore');

        $result = (new Builder())
            ->addSource(self::examplePath('petstore/annotations'))
            ->setLogger($this->trackingLogger())
            ->withGenerator(function (Generator $generator): void {
                $generator->setAnalyser($this->getAnalyzer());
                $generator->setTypeResolver($this->getTypeResolver());
            })
            ->build();

        $this->assertTrue($result->isValid());
        $this->assertNotEmpty($result->toArray());
        $this->assertNotEmpty($result->toJson());
        $this->assertNotEmpty($result->toYaml());
    }

    public function testIsValidReportsCompilerErrors(): void
    {
        // no OA\Info anywhere -> the compiler reports "info is required"
        $this->expectLogEntry('info is required', 'error');

        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource((string) self::fixture('Assembler/SimpleController.php'))
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertNotEmpty($result->toArray(), 'a document is still produced');
        $this->assertFalse($result->isValid(), 'errors must make the result invalid');
    }

    public function testBuildMatchesGenerator(): void
    {
        $this->registerExampleClassloader('petstore');
        $sourceDir = self::examplePath('petstore/annotations');

        $generatorOutput = (new Generator())
            ->setAnalyser($this->getAnalyzer())
            ->setTypeResolver($this->getTypeResolver())
            ->generate([$sourceDir]);

        $builderResult = (new Builder())
            ->setMode(Mode::CLASSIC)
            ->addSource($sourceDir)
            ->withGenerator(function (Generator $generator): void {
                $generator->setAnalyser($this->getAnalyzer());
                $generator->setTypeResolver($this->getTypeResolver());
            })
            ->build();

        $this->assertSpecEquals($generatorOutput?->toYaml(), $builderResult->toYaml());
    }

    public function testBuildWithVersion(): void
    {
        $this->registerExampleClassloader('petstore');

        $result = (new Builder())
            ->addSource(self::examplePath('petstore/annotations'))
            ->setVersion('3.1.0')
            ->withGenerator(function (Generator $generator): void {
                $generator->setAnalyser($this->getAnalyzer());
                $generator->setTypeResolver($this->getTypeResolver());
            })
            ->build();

        $this->assertTrue($result->isValid());
        $spec = $result->toArray();
        $this->assertSame('3.1.0', $spec['openapi']);
    }

    public function testBuildWithFinder(): void
    {
        $this->registerExampleClassloader('petstore');

        $result = (new Builder())
            ->addSource(new SourceFinder(self::examplePath('petstore/annotations')))
            ->withGenerator(function (Generator $generator): void {
                $generator->setAnalyser($this->getAnalyzer());
                $generator->setTypeResolver($this->getTypeResolver());
            })
            ->build();

        $this->assertTrue($result->isValid());
    }

    public function testBuildFiles(): void
    {
        $this->registerExampleClassloader('petstore');

        $result = (new Builder())
            ->addSource(self::examplePath('petstore/annotations'))
            ->withGenerator(function (Generator $generator): void {
                $generator->setAnalyser($this->getAnalyzer());
                $generator->setTypeResolver($this->getTypeResolver());
            })
            ->build();

        $this->assertNotEmpty($result->files());
        foreach ($result->files() as $file) {
            $this->assertFileExists($file);
        }
    }

    public function testBuildReflectors(): void
    {
        $this->registerExampleClassloader('webhooks', 'spec');

        $reflectorResult = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource([
                new \ReflectionClass(WebhooksSpec\NewPetWebhook::class),
                new \ReflectionClass(WebhooksSpec\OpenApiSpec::class),
                new \ReflectionClass(WebhooksSpec\Pet::class),
            ])
            ->build();

        $fileResult = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(new SourceFinder(self::examplePath('webhooks/spec')))
            ->build();

        $this->assertSpecEquals($fileResult->toArray(), $reflectorResult->toArray());
    }

    public function testBuildReflectorsHybrid(): void
    {
        $this->registerExampleClassloader('webhooks', 'attributes');

        $reflectorResult = (new Builder())
            ->setMode(Mode::HYBRID)
            ->addSource([
                new \ReflectionClass(WebhooksAttributes\OpenApiSpec::class),
                new \ReflectionClass(WebhooksAttributes\Pet::class),
            ])
            ->build();

        $fileResult = (new Builder())
            ->setMode(Mode::HYBRID)
            ->addSource(new SourceFinder(self::examplePath('webhooks/attributes')))
            ->build();

        // the info lives on a classic attribute, so only the classic half can have read it
        $this->assertSame('Webhook Example', $reflectorResult->toArray()['info']['title'] ?? null);
        $this->assertSpecEquals($fileResult->toArray(), $reflectorResult->toArray());
    }

    /**
     * A class from `eval()` has no file to scan. Spec reads its attributes through reflection
     * alone, and the classic half of a hybrid build, which needs the file, skips it.
     */
    public function testBuildReadsAClassFromEval(): void
    {
        static $instance = null;
        $instance ??= eval('namespace OpenApi\Tests;
            use OpenApi\Attributes as OAT;
            use OpenApi\Spec as OA;
            class EvaluatedController {
                #[OAT\Get(path: "/classic", responses: [new OAT\Response(response: 200, description: "ok")])]
                public function classic() {}
                #[OA\Operation\Get(path: "/spec", responses: [new OA\Response(response: 200, description: "ok")])]
                public function spec() {}
            }
            return new EvaluatedController();');
        $class = $instance::class;

        $this->expectLogEntry("Skipping {$class}: its source file ", 'warning');
        $this->allowLogEntry('info is required');

        foreach ([Mode::SPEC, Mode::HYBRID] as $mode) {
            $result = (new Builder())
                ->setMode($mode)
                ->addSource(new \ReflectionClass($instance))
                ->setLogger($this->trackingLogger())
                ->build();

            $this->assertSame(['/spec'], array_keys($result->toArray()['paths']), $mode->value);
        }
    }

    public function testBuildReportsANonClassReflectorSpec(): void
    {
        $this->expectLogEntry('Skipping ReflectionMethod source ' . Fixtures\Assembler\SimpleController::class . '::', 'warning');
        $this->allowLogEntry('info is required', 'At least one of paths, webhooks, or components is required');

        $method = (new \ReflectionClass(Fixtures\Assembler\SimpleController::class))->getMethods()[0];

        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource($method)
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertArrayNotHasKey('paths', $result->toArray());
    }

    public function testBuildEmptySources(): void
    {
        $this->expectLogEntry('Required @OA\Info() not found');
        $this->expectLogEntry('Required @OA\PathItem() not found');

        $result = (new Builder())
            ->setMode(Mode::CLASSIC)
            ->setSources([])
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertEmpty($result->files());
    }

    public function testBuildLogsClassicWarnings(): void
    {
        $this->expectLogEntry('Required @OA\Info() not found', 'warning');
        $this->expectLogEntry('Required @OA\PathItem() not found', 'warning');

        (new Builder())
            ->setMode(Mode::CLASSIC)
            ->setSources([])
            ->setLogger($this->trackingLogger())
            ->build();
    }

    /**
     * Spec reads only a constructor's parameters, as promoted properties. Anything else on it is
     * left out of the document, with a warning naming the attributes and where to put them.
     */
    public function testBuildReportsConstructorAttributes(): void
    {
        require_once self::fixture('PHP/ConstructorAttributes.php');

        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(self::fixture('PHP/ConstructorAttributes.php'))
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertArrayNotHasKey('/constructor', $result->toArray()['paths']);
        $this->assertArrayHasKey('/method', $result->toArray()['paths']);

        $this->expectLogEntry('ignored on a constructor', 'warning');

        $reported = array_column($this->recordedLogEntries(), 'message');
        $this->assertStringStartsWith(
            'OpenApi\Spec\Operation\Post, OpenApi\Spec\Response ignored on a constructor, declare them on the class in ',
            $reported[0],
        );
        $this->assertStringContainsString('ConstructorAttributesController::__construct()', $reported[0]);
    }

    /**
     * A component declared on a member is one only in a class carrying `#[OA\Components]`.
     * Without it, a keyed one is left out of the document, with a warning naming the member.
     */
    public function testBuildReportsStrayComponents(): void
    {
        require_once self::fixture('PHP/StrayComponents.php');

        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(self::fixture('PHP/StrayComponents.php'))
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertArrayNotHasKey('components', $result->toArray());

        $this->expectLogEntry('OpenApi\Spec\Schema "onProperty" ignored, a component declared on a member needs OpenApi\Spec\Components on its class, in ' . Fixtures\PHP\StrayComponents::class . '::$property in ', 'warning');
        $this->expectLogEntry('OpenApi\Spec\Schema "onMethod" ignored, a component declared on a member needs OpenApi\Spec\Components on its class, in ' . Fixtures\PHP\StrayComponents::class . '::method() in ', 'warning');
    }

    /**
     * A class that exists by name but will not load — its parent is missing — must cost
     * the scan one class, not the whole run: https://github.com/zircote/swagger-php/issues/1600.
     */
    public function testBuildSkipsClassThatWillNotLoadClassic(): void
    {
        $this->expectLogEntry(
            'Skipping unloadable OpenApi\Tests\Fixtures\PHP\UnloadableClassRepro\BrokenStub: Class "OpenApi\Tests\Fixtures\PHP\UnloadableClassRepro\DeliberatelyAbsentParent" not found',
            'warning',
        );

        $result = (new Builder())
            ->setMode(Mode::CLASSIC)
            ->addSource(self::fixture('PHP/UnloadableClassRepro'))
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertStringContainsString('getEndpoint', $result->toYaml());
    }

    public function testBuildSkipsClassThatWillNotLoadHybrid(): void
    {
        // hybrid scans twice — the spec-side Assembler pass and doHybridAssemble()'s
        // classic pass — so the broken class is independently skipped, and warned about,
        // by each
        $this->expectLogEntry('Skipping unloadable', 'warning');
        $this->expectLogEntry('Skipping unloadable', 'warning');

        $result = (new Builder())
            ->setMode(Mode::HYBRID)
            ->addSource(self::fixture('PHP/UnloadableClassRepro'))
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertStringContainsString('getEndpoint', $result->toYaml());
    }

    public function testBuildSkipsClassThatWillNotLoadSpec(): void
    {
        $this->expectLogEntry('Skipping unloadable', 'warning');
        $this->allowLogEntry('info is required', 'At least one of paths, webhooks, or components is required');

        (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(self::fixture('PHP/UnloadableClassRepro'))
            ->setLogger($this->trackingLogger())
            ->build();
    }

    /**
     * The same holds for a class the scan never sees, reached by resolving a reference to it.
     */
    public function testBuildSkipsReferencedClassThatWillNotLoadSpec(): void
    {
        $this->expectLogEntry('Skipping unloadable', 'warning');
        $this->allowLogEntry('info is required', 'At least one of paths, webhooks, or components is required', 'Ref: unresolved reference');

        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(new \ReflectionClass(Fixtures\Resolver\Warehouse::class))
            // nothing references the schemas from a path; keep them to assert on
            ->withAugmenters(static function (Pipeline $augmenters): void {
                $augmenters->get(Augmenter\Cleanup::class)?->setEnabled(false);
            })
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertArrayHasKey('Manufacturer', $result->toArray()['components']['schemas']);
    }

    /**
     * A skipped class is reported to the attached logger, and the document left behind is
     * still valid. Both skips: the scan's, for a class that will not load, and assembly's, for
     * one whose attributes fail.
     */
    public function testBuildLogsSkippedClassesAndStaysValidSpec(): void
    {
        $this->expectLogEntry('Skipping unloadable ' . Fixtures\PHP\SkippedClasses\WillNotReflect::class . ': ', 'warning');
        $this->expectLogEntry('Skipping unloadable ' . Fixtures\PHP\SkippedClasses\WillNotCollect::class . ': ', 'warning');

        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(self::fixture('PHP/SkippedClasses'))
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertSame(['/endpoint'], array_keys($result->toArray()['paths']));
        $this->assertTrue($result->isValid(), 'a skip is a build message, not a validation error');
    }

    public function testCompilerDiagnosticsReachTheConfiguredLogger(): void
    {
        $logger = new CollectingLogger();

        // spec mode: an empty source set leaves the compiler with nothing to validate
        (new Builder())
            ->setMode(Mode::SPEC)
            ->setSources([])
            ->setVersion('3.0.0')
            ->setLogger($logger)
            ->build();

        $this->assertContains('info is required', array_column($logger->entries(), 'message'));
    }

    public function testGetAugmentersReturnsDefaultPipeline(): void
    {
        $builder = new Builder();
        $pipeline = $builder->getAugmenters();

        $found = false;
        $pipeline->walk(function (callable $pipe) use (&$found): void {
            if ($pipe instanceof OperationIds) {
                $found = true;
            }
        });

        $this->assertTrue($found, 'Default augmenters should include OperationId');
    }

    public function testPipelineGetReturnsTypedInstance(): void
    {
        $builder = new Builder();
        $operationId = $builder->getAugmenters()->get(OperationIds::class);

        $this->assertInstanceOf(OperationIds::class, $operationId);

        $operationId->setHash(false);

        $rc = new \ReflectionProperty($operationId, 'hash');
        $this->assertFalse($rc->getValue($operationId));
    }
}
