<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Tests\Concerns\UsesExamples;

class CommandlineTest extends OpenApiTestCase
{
    use UsesExamples;

    public function testStdout(): void
    {
        $basePath = $this->examplePath('petstore');
        $path = "$basePath/annotations";
        exec(__DIR__ . '/../bin/openapi --bootstrap ' . __DIR__ . '/cl_bootstrap.php --format yaml ' . escapeshellarg($path) . ' 2> /dev/null', $output, $retval);
        $this->assertSame(0, $retval, implode(PHP_EOL, $output));
        $yaml = implode(PHP_EOL, $output);
        $this->assertSpecEquals(file_get_contents($this->getSpecFilename('petstore')), $yaml);
    }

    public function testOutputTofile(): void
    {
        $basePath = $this->examplePath('petstore');
        $path = "$basePath/annotations";
        $filename = sys_get_temp_dir() . '/swagger-php-clitest.yaml';
        exec(__DIR__ . '/../bin/openapi --bootstrap ' . __DIR__ . '/cl_bootstrap.php --format yaml -o ' . escapeshellarg($filename) . ' ' . escapeshellarg($path) . ' 2> /dev/null', $output, $retval);
        $this->assertSame(0, $retval, implode(PHP_EOL, $output));
        $this->assertCount(0, $output, 'No output to stdout');
        $yaml = file_get_contents($filename);
        unlink($filename);
        $this->assertSpecEquals(file_get_contents($this->getSpecFilename('petstore')), $yaml);
    }

    public function testAddProcessor(): void
    {
        $basePath = $this->examplePath('petstore');
        $path = "$basePath/annotations";
        $cmd = __DIR__ . '/../bin/openapi --bootstrap ' . __DIR__ . '/cl_bootstrap.php --processor OperationId --format yaml ' . escapeshellarg($path);
        exec($cmd . ' 2> /dev/null', $output, $retval);
        $this->assertSame(0, $retval, $cmd . PHP_EOL . implode(PHP_EOL, $output));
    }

    public function testExcludeListWarning(): void
    {
        $basePath = $this->examplePath('petstore');
        $path = "$basePath/annotations";
        exec(__DIR__ . '/../bin/openapi -e foo,bar ' . escapeshellarg($path) . ' 2>&1', $output, $retval);
        $this->assertSame(1, $retval);
        $output = implode(PHP_EOL, $output);
        $this->assertStringContainsString('Comma-separated exclude paths are deprecated', $output);
    }

    public function testMissingArg(): void
    {
        $basePath = $this->examplePath('petstore');
        $path = "$basePath/annotations";
        exec(__DIR__ . '/../bin/openapi ' . escapeshellarg($path) . ' -e 2>&1', $output, $retval);
        $this->assertSame(1, $retval);
        $output = implode(PHP_EOL, $output);
        $this->assertStringContainsString('Error: Missing argument for "-e"', $output);
    }
}
