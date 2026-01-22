<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Generator;
use OpenApi\Processors\OperationId;
use OpenApi\SourceFinder;
use OpenApi\Tests\Concerns\UsesExamples;
use PHPUnit\Framework\Attributes\DataProvider;

class GeneratorTest extends OpenApiTestCase
{
    use UsesExamples;

    public static function sourcesProvider(): iterable
    {
        $name = 'petstore';
        $sourceDir = static::examplePath("$name/annotations");

        yield 'dir-list' => [$name, [$sourceDir]];
        yield 'finder' => [$name, new SourceFinder($sourceDir)];
        yield 'finder-list' => [$name, [new SourceFinder($sourceDir)]];
    }

    #[DataProvider('sourcesProvider')]
    public function testGenerate(string $name, iterable $sources): void
    {
        $this->registerExampleClassloader($name);

        $openapi = (new Generator())
            ->setAnalyser($this->getAnalyzer())
            ->setTypeResolver($this->getTypeResolver())
            ->generate($sources);

        $this->assertSpecEquals(file_get_contents(static::getSpecFilename($name)), $openapi);
    }

    public function testScanInvalidSource(): void
    {
        $this->assertOpenApiLogEntryContains('Skipping invalid source: /tmp/__swagger_php_does_not_exist__');
        $this->assertOpenApiLogEntryContains('Required @OA\PathItem() not found');
        $this->assertOpenApiLogEntryContains('Required @OA\Info() not found');

        (new Generator($this->getTrackingLogger()))
            ->setAnalyser($this->getAnalyzer())
            ->setTypeResolver($this->getTypeResolver())
            ->generate(['/tmp/__swagger_php_does_not_exist__']);
    }

    public static function processorCases(): iterable
    {
        return [
            [new OperationId(), true],
            [new class (false) extends OperationId {
            }, false],
        ];
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

    protected function assertOperationIdHash(Generator $generator, bool $expected): void
    {
        $generator->getProcessorPipeline()->walk(function ($processor) use ($expected) {
            if ($processor instanceof OperationId) {
                $this->assertEquals($expected, $processor->isHash());
            }
        });
    }

    public static function configCases(): iterable
    {
        return [
            'default' => [[], true],
            'nested' => [['operationId' => ['hash' => false]], false],
            'dots-kv' => [['operationId.hash' => false], false],
            'dots-string' => [['operationId.hash=false'], false],
        ];
    }

    #[DataProvider('configCases')]
    public function testConfig(array $config, bool $expected): void
    {
        $generator = new Generator();
        $this->assertOperationIdHash($generator, true);

        $generator->setConfig($config);
        $this->assertOperationIdHash($generator, $expected);
    }

    public function testDefaultConfig(): void
    {
        $walker = function (callable $pipe) use (&$collectedConfig): void {
            $rc = new \ReflectionClass($pipe);
            $ctorparams = [];
            foreach ($rc->getConstructor()?->getParameters() ?? [] as $rparam) {
                $ctorparams[$rparam->getName()] = $rparam;
            }

            $processorKey = lcfirst($rc->getShortName());
            foreach ($rc->getMethods() as $rm) {
                if ($rm->isPublic() && str_starts_with($rm->getName(), 'set')) {
                    $name = lcfirst(substr($rm->getName(), 3));
                    if (array_key_exists($name, $ctorparams)) {
                        $collectedConfig[$processorKey][$name] = $ctorparams[$name]->getDefaultValue();
                    }
                }
            }
        };

        $collectedConfig = [];
        $generator = new Generator();
        $generator->getProcessorPipeline()->walk($walker);

        // excludes generator config
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys($generator->getDefaultConfig(), $collectedConfig, ['generator']);
    }
}
