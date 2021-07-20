<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Analysis;
use OpenApi\Generator;
use OpenApi\Logger;
use OpenApi\Processors\OperationId;
use OpenApi\Util;

class GeneratorTest extends OpenApiTestCase
{
    const SOURCE_DIR = __DIR__ . '/../Examples/swagger-spec/petstore-simple';

    public function sourcesProvider()
    {
        $sourceDir = self::SOURCE_DIR;
        $sources = [
            $sourceDir . '/SimplePet.php',
            $sourceDir . '/SimplePetsController.php',
            $sourceDir . '/api.php',
        ];

        return [
            'dir-list' => [$sourceDir, [$sourceDir]],
            'file-list' => [$sourceDir, $sources],
            'finder' => [$sourceDir, Util::finder($sourceDir)],
            'finder-list' => [$sourceDir, [Util::finder($sourceDir)]],
        ];
    }

    /**
     * @dataProvider sourcesProvider
     */
    public function testScan(string $sourceDir, iterable $sources)
    {
        $openapi = (new Generator())
            ->scan($sources);

        $this->assertSpecEquals(file_get_contents(sprintf('%s/%s.yaml', $sourceDir, basename($sourceDir))), $openapi);
    }

    public function testLogger()
    {
        // reset test logger
        Logger::getInstance()->log = function ($entry, $type) {
            $this->fail('Wrong logger');
        };

        $this->assertOpenApiLogEntryContains('The annotation @SWG\Definition() is deprecated.');
        $this->assertOpenApiLogEntryContains('Required @OA\Info() not found');
        $this->assertOpenApiLogEntryContains('Required @OA\PathItem() not found');

        (new Generator($this->getPsrLogger(true)))
            ->setAliases(['swg' => 'OpenApi\Annotations'])
            ->generate($this->fixtures('Deprecated.php'));
    }

    public function processorCases()
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
                $this->assertSpecEquals($expected, $processor->isHash());
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
