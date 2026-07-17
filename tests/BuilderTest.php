<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Augmenter\OperationIds;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Generator;
use OpenApi\Tests\Concerns\UsesExamples;
use OpenApi\Utils\SourceFinder;
use Psr\Log\NullLogger;

final class BuilderTest extends OpenApiTestCase
{
    use UsesExamples;

    public function testBuild(): void
    {
        $this->registerExampleClassloader('petstore');

        $result = (new Builder())
            ->addSource(self::examplePath('petstore/annotations'))
            ->setLogger($this->getTrackingLogger())
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

        $this->assertSpecEquals($generatorOutput, $builderResult->toYaml());
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

    public function testBuildEmptySources(): void
    {
        $this->assertOpenApiLogEntryContains('Required @OA\Info() not found');
        $this->assertOpenApiLogEntryContains('Required @OA\PathItem() not found');

        $result = (new Builder())
            ->setMode(Mode::CLASSIC)
            ->setSources([])
            ->setLogger($this->getTrackingLogger())
            ->build();

        $this->assertEmpty($result->files());
    }

    public function testBuildCollectsWarnings(): void
    {
        $result = (new Builder())
            ->setMode(Mode::CLASSIC)
            ->setSources([])
            ->setLogger(new NullLogger())
            ->build();

        $this->assertNotEmpty($result->warnings());
        $this->assertContains('Required @OA\Info() not found', $result->warnings());
        $this->assertContains('Required @OA\PathItem() not found', $result->warnings());
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
