<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

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
            ->setAnalyser($this->getAnalyzer())
            ->generate($sources);

        $this->assertSpecEquals(file_get_contents(sprintf('%s/%s.yaml', $sourceDir, basename($sourceDir))), $openapi);
    }

    public function testScanInvalidSource(): void
    {
        $this->assertOpenApiLogEntryContains('Skipping invalid source: /tmp/__swagger_php_does_not_exist__');
        $this->assertOpenApiLogEntryContains('The OpenAPI document must contain paths field');

        (new Generator($this->getTrackingLogger()))
            ->setAnalyser($this->getAnalyzer())
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
    public function testUpdateProcessor($p, bool $expected): void
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
        $processor = function (Analysis $analysis): void {
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

    protected function assertOperationIdHash(Generator $generator, bool $expected): void
    {
        foreach ($generator->getProcessors() as $processor) {
            if ($processor instanceof OperationId) {
                $this->assertEquals($expected, $processor->isHash());
            }
        }
    }

    public function configCases(): iterable
    {
        return [
            'default' => [[], true],
            'nested' => [['operationId' => ['hash' => false]], false],
            'dots-kv' => [['operationId.hash' => false], false],
            'dots-string' => [['operationId.hash=false'], false],
        ];
    }

    /**
     * @dataProvider configCases
     */
    public function testConfig(array $config, bool $expected): void
    {
        $generator = new Generator();
        $this->assertOperationIdHash($generator, true);

        $generator->setConfig($config);
        $this->assertOperationIdHash($generator, $expected);
    }

    public function testCallableProcessor(): void
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
