<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Analysers\TokenAnalyser;
use OpenApi\Analysis;
use OpenApi\Generator;
use OpenApi\Processors\OperationId;
use OpenApi\Util;

class GeneratorTest extends OpenApiTestCase
{
    public function sourcesProvider(): iterable
    {
        $sourceDir = $this->example('swagger-spec/petstore-simple');

        yield 'dir-list' => [$sourceDir, [$sourceDir]];
        yield 'file-list' => [$sourceDir, ["$sourceDir/SimplePet.php", "$sourceDir/SimplePetsController.php", "$sourceDir/OpenApiSpec.php"]];
        yield 'finder' => [$sourceDir, Util::finder($sourceDir)];
        yield 'finder-list' => [$sourceDir, [Util::finder($sourceDir)]];
    }

    /**
     * @dataProvider sourcesProvider
     */
    public function testScan(string $sourceDir, iterable $sources): void
    {
        $openapi = (new Generator())
            ->setAnalyser(new TokenAnalyser())
            ->generate($sources);

        $this->assertSpecEquals(file_get_contents(sprintf('%s/%s.yaml', $sourceDir, basename($sourceDir))), $openapi);
    }

    public function testScanInvalidSource(): void
    {
        $this->assertOpenApiLogEntryContains('Skipping invalid source: /tmp/__swagger_php_does_not_exist__');
        $this->assertOpenApiLogEntryContains('Required @OA\Info() not found');
        $this->assertOpenApiLogEntryContains('Required @OA\PathItem() not found');

        (new Generator($this->getTrackingLogger()))
            ->setAnalyser(new TokenAnalyser())
            ->generate(['/tmp/__swagger_php_does_not_exist__']);
    }

    public function processorCases(): iterable
    {
        return [
            [new OperationId(), true],
            [new class(false) extends OperationId {
            }, false],
        ];
    }

    /**
     * @dataProvider processorCases
     */
    public function testUpdateProcessor($p, $expected): void
    {
        $generator = (new Generator())
            ->updateProcessor($p);
        foreach ($generator->getProcessors() as $processor) {
            if ($processor instanceof OperationId) {
                $this->assertEquals($expected, $processor->isHash());
            }
        }
    }

    public function testAddProcessor(): void
    {
        $generator = new Generator();
        $processors = $generator->getProcessors();
        $generator->addProcessor(function (Analysis $analysis) {
        });

        $this->assertLessThan(count($generator->getProcessors()), count($processors));
    }

    public function testAddAlias(): void
    {
        $generator = new Generator();
        $generator->addAlias('foo', 'Foo\\Bar');

        $this->assertEquals(['oa' => 'OpenApi\\Annotations', 'foo' => 'Foo\\Bar'], $generator->getAliases());
    }

    public function testAddNamespace(): void
    {
        $generator = new Generator();
        $generator->addNamespace('Foo\\Bar\\');

        $this->assertEquals(['OpenApi\\Annotations\\', 'Foo\\Bar\\'], $generator->getNamespaces());
    }

    public function testRemoveProcessor(): void
    {
        $generator = new Generator();
        $processors = $generator->getProcessors();
        $processor = function (Analysis $analysis) {
        };
        $generator->addProcessor($processor);
        $generator->removeProcessor($processor);

        $this->assertEquals($processors, $generator->getProcessors());
    }

    public function testRemoveProcessorNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Generator())->removeProcessor(function () {
        });
    }

    protected function assertOperationIdHash(Generator $generator, bool $expected)
    {
        foreach ($generator->getProcessors() as $processor) {
            if ($processor instanceof OperationId) {
                $this->assertEquals($expected, $processor->isHash());
            }
        }
    }

    public function testConfig()
    {
        $generator = new Generator();
        $this->assertOperationIdHash($generator, true);

        $generator->setConfig(['operationId' => ['hash' => false]]);
        $this->assertOperationIdHash($generator, false);
    }

    public function testCallableProcessor()
    {
        $generator = new Generator();
        // not the default
        $operationId = new OperationId(false);
        $generator->addProcessor(function (Analysis $analysis) use ($operationId) {
            $operationId($analysis);
        });

        $this->assertOperationIdHash($generator, true);
        $this->assertFalse($operationId->isHash());
    }
}
