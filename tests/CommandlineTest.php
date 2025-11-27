<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Tests\Concerns\UsesExamples;

class CommandlineTest extends OpenApiTestCase
{
    use UsesExamples;

    private function getCommandToExecute(string $cmd, ?string $devNullRedir = null): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'php ' . $cmd;
            $devNull = 'NUL';
        } else {
            $devNull = '/dev/null';
        }
        if ($devNullRedir) {
            $cmd .= " $devNullRedir $devNull";
        }

        return $cmd;
    }

    public function testStdout(): void
    {
        $basePath = static::examplePath('petstore');
        $path = "$basePath/annotations";
        exec($this->getCommandToExecute(__DIR__ . '/../bin/openapi --bootstrap ' . __DIR__ . '/cl_bootstrap.php --format yaml ' . escapeshellarg($path), '2>'), $output, $retval);
        $this->assertSame(0, $retval, implode(PHP_EOL, $output));
        $yaml = implode(PHP_EOL, $output);
        $this->assertSpecEquals(file_get_contents(static::getSpecFilename('petstore')), $yaml);
    }

    public function testOutputToFile(): void
    {
        $basePath = static::examplePath('petstore');
        $path = "$basePath/annotations";
        $filename = sys_get_temp_dir() . '/swagger-php-clitest.yaml';
        exec($this->getCommandToExecute(__DIR__ . '/../bin/openapi --bootstrap ' . __DIR__ . '/cl_bootstrap.php --format yaml -o ' . escapeshellarg($filename) . ' ' . escapeshellarg($path), '2>'), $output, $retval);
        $this->assertSame(0, $retval, implode(PHP_EOL, $output));
        $this->assertCount(0, $output, 'No output to stdout');
        $yaml = file_get_contents($filename);
        unlink($filename);
        $this->assertSpecEquals(file_get_contents(static::getSpecFilename('petstore')), $yaml);
    }

    public function testAddProcessor(): void
    {
        $basePath = static::examplePath('petstore');
        $path = "$basePath/annotations";
        $cmd = __DIR__ . '/../bin/openapi --bootstrap ' . __DIR__ . '/cl_bootstrap.php --add-processor OperationId --format yaml ' . escapeshellarg($path);
        exec($this->getCommandToExecute($cmd, '2>'), $output, $retval);
        $this->assertSame(0, $retval, $cmd . PHP_EOL . implode(PHP_EOL, $output));
    }

    public function testRemoveProcessor(): void
    {
        $basePath = static::examplePath('petstore');
        $path = "$basePath/annotations";
        $cmd = __DIR__ . '/../bin/openapi --bootstrap ' . __DIR__ . '/cl_bootstrap.php --remove-processor OperationId --format yaml ' . escapeshellarg($path);
        exec($this->getCommandToExecute($cmd, '2>'), $output, $retval);
        $this->assertSame(0, $retval, $cmd . PHP_EOL . implode(PHP_EOL, $output));
    }

    public function testExcludeListWarning(): void
    {
        $basePath = static::examplePath('petstore');
        $path = "$basePath/annotations";
        exec($this->getCommandToExecute(__DIR__ . '/../bin/openapi -e foo,bar ' . escapeshellarg($path) . ' 2>&1'), $output, $retval);
        $this->assertSame(1, $retval);
        $output = implode(PHP_EOL, $output);
        $this->assertStringContainsString('Comma-separated exclude paths are deprecated', $output);
    }

    public function testMissingArg(): void
    {
        $basePath = static::examplePath('petstore');
        $path = "$basePath/annotations";
        exec($this->getCommandToExecute(__DIR__ . '/../bin/openapi ' . escapeshellarg($path) . ' -e 2>&1'), $output, $retval);
        $this->assertSame(1, $retval);
        $output = implode(PHP_EOL, $output);
        $this->assertStringContainsString('Error: Missing argument for "-e"', $output);
    }
}
