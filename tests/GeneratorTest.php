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
        yield 'file-list' => [$sourceDir, ["$sourceDir/SimplePet.php", "$sourceDir/SimplePetsController.php", "$sourceDir/api.php"]];
        yield 'finder' => [$sourceDir, Util::finder($sourceDir)];
        yield 'finder-list' => [$sourceDir, [Util::finder($sourceDir)]];
    }

    /**
     * @dataProvider sourcesProvider
     */
    public function testScan(string $sourceDir, iterable $sources)
    {
        $openapi = (new Generator())
            ->setAnalyser(new TokenAnalyser())
            ->generate($sources);

        $this->assertSpecEquals(file_get_contents(sprintf('%s/%s.yaml', $sourceDir, basename($sourceDir))), $openapi);
    }

    public function processorCases(): iterable
    {
        return [
            [new OperationId(false), false],
            [new OperationId(true), true],
            [new class(false) extends OperationId {
            }, false],
        ];
    }

    /**
     * @dataProvider processorCases
     */
    public function testUpdateProcessor($p, $expected)
    {
        $generator = (new Generator())
            ->updateProcessor($p);
        foreach ($generator->getProcessors() as $processor) {
            if ($processor instanceof OperationId) {
                $this->assertEquals($expected, $processor->isHash());
            }
        }
    }

    public function testAddProcessor()
    {
        $generator = new Generator();
        $processors = $generator->getProcessors();
        $generator->addProcessor(function (Analysis $analysis) {
        });

        $this->assertLessThan(count($generator->getProcessors()), count($processors));
    }

    public function testAddAlias()
    {
        $generator = new Generator();
        $generator->addAlias('foo', 'Foo\\Bar');

        $this->assertEquals(['oa' => 'OpenApi\\Annotations', 'foo' => 'Foo\\Bar'], $generator->getAliases());
    }

    public function testAddNamespace()
    {
        $generator = new Generator();
        $generator->addNamespace('Foo\\Bar\\');

        $this->assertEquals(['OpenApi\\Annotations\\', 'Foo\\Bar\\'], $generator->getNamespaces());
    }

    public function testRemoveProcessor()
    {
        $generator = new Generator();
        $processors = $generator->getProcessors();
        $processor = function (Analysis $analysis) {
        };
        $generator->addProcessor($processor);
        $generator->removeProcessor($processor);

        $this->assertEquals($processors, $generator->getProcessors());
    }
}
