<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Tests\Concerns\UsesExamples;
use PHPUnit\Framework\Attributes\DataProvider;

final class CommandlineTest extends OpenApiTestCase
{
    use UsesExamples;

    public function testStdout(): void
    {
        $basePath = self::examplePath('petstore');
        $path = "{$basePath}/annotations";
        exec($this->getCommandToExecute(__DIR__ . '/../bin/openapi --bootstrap ' . __DIR__ . '/cl_bootstrap.php --format yaml ' . escapeshellarg($path), '2>'), $output, $retval);
        $this->assertSame(0, $retval, implode(PHP_EOL, $output));
        $yaml = implode(PHP_EOL, $output);
        $this->assertSpecEquals(file_get_contents(self::getSpecFilename('petstore')), $yaml);
    }

    public function testOutputToFile(): void
    {
        $basePath = self::examplePath('petstore');
        $path = "{$basePath}/annotations";
        $filename = sys_get_temp_dir() . '/swagger-php-clitest.yaml';
        exec($this->getCommandToExecute(__DIR__ . '/../bin/openapi --bootstrap ' . __DIR__ . '/cl_bootstrap.php --format yaml -o ' . escapeshellarg($filename) . ' ' . escapeshellarg($path), '2>'), $output, $retval);
        $this->assertSame(0, $retval, implode(PHP_EOL, $output));
        $this->assertCount(0, $output, 'No output to stdout');
        $yaml = file_get_contents($filename);
        unlink($filename);
        $this->assertSpecEquals(file_get_contents(self::getSpecFilename('petstore')), $yaml);
    }

    /**
     * Without `-o` the document goes to stdout, so diagnostics must not: a warning there ends
     * up as the first line of `openapi src > openapi.yaml`.
     */
    public function testDiagnosticsGoToStderr(): void
    {
        $fixture = __DIR__ . '/Fixtures/DuplicateOperationId.php';
        $cmd = $this->getCommandToExecute(__DIR__ . '/../bin/openapi --bootstrap ' . escapeshellarg($fixture) . ' --format yaml ' . escapeshellarg($fixture));

        $process = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        $this->assertIsResource($process);
        $stdout = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $this->assertSame(0, proc_close($process), $stderr);

        $this->assertStringStartsWith('openapi: ', $stdout);
        $this->assertStringNotContainsString('operationId must be unique', $stdout);
        $this->assertStringContainsString('operationId must be unique', $stderr);
    }

    public function testAddProcessor(): void
    {
        $basePath = self::examplePath('petstore');
        $path = "{$basePath}/annotations";
        $cmd = __DIR__ . '/../bin/openapi --bootstrap ' . __DIR__ . '/cl_bootstrap.php --add-processor OperationId --format yaml ' . escapeshellarg($path);
        exec($this->getCommandToExecute($cmd, '2>'), $output, $retval);
        $this->assertSame(0, $retval, $cmd . PHP_EOL . implode(PHP_EOL, $output));
    }

    public function testRemoveProcessor(): void
    {
        $basePath = self::examplePath('petstore');
        $path = "{$basePath}/annotations";
        $cmd = __DIR__ . '/../bin/openapi --bootstrap ' . __DIR__ . '/cl_bootstrap.php --remove-processor OperationId --format yaml ' . escapeshellarg($path);
        exec($this->getCommandToExecute($cmd, '2>'), $output, $retval);
        $this->assertSame(0, $retval, $cmd . PHP_EOL . implode(PHP_EOL, $output));
    }

    public function testMissingArg(): void
    {
        $basePath = self::examplePath('petstore');
        $path = "{$basePath}/annotations";
        exec($this->getCommandToExecute(__DIR__ . '/../bin/openapi ' . escapeshellarg($path) . ' -e 2>&1'), $output, $retval);
        $this->assertSame(1, $retval);
        $output = implode(PHP_EOL, $output);
        $this->assertStringContainsString('The "--exclude" option requires a value.', $output);
    }

    /**
     * @return iterable<mixed>
     */
    public static function invalidEnumOptionCases(): iterable
    {
        yield 'format' => ['-f xml', 'The value "xml" is not valid for the "format" option. Supported values are "json", "yaml", "auto".'];
        yield 'mode' => ['-m bogus', 'The value "bogus" is not valid for the "mode" option. Supported values are "classic", "hybrid", "spec".'];
    }

    #[DataProvider('invalidEnumOptionCases')]
    public function testInvalidEnumOption(string $args, string $expected): void
    {
        $basePath = self::examplePath('petstore');
        $path = "{$basePath}/annotations";
        exec($this->getCommandToExecute(__DIR__ . '/../bin/openapi ' . $args . ' ' . escapeshellarg($path) . ' 2>&1'), $output, $retval);

        $this->assertSame(1, $retval);
        $this->assertStringContainsString($expected, (string) preg_replace('/\s+/', ' ', implode(' ', $output)));
    }

    /**
     * @return iterable<mixed>
     */
    public static function versionCases(): iterable
    {
        yield 'default' => ['', '3.1.0'];
        yield 'override' => ['--version=3.0.3', '3.0.3'];
    }

    #[DataProvider('versionCases')]
    public function testVersionDefault(string $args, string $expectedVersion): void
    {
        $fixture = $this->fixture('Explicit310.php');
        $cmd = __DIR__ . '/../bin/openapi --bootstrap ' . $fixture . " {$args} " . escapeshellarg((string) $fixture);
        exec($this->getCommandToExecute($cmd, '2>'), $output, $retval);
        $this->assertSame(0, $retval, $cmd . PHP_EOL . implode(PHP_EOL, $output));
        $this->assertStringContainsString("openapi: {$expectedVersion}", implode(PHP_EOL, $output));
    }

    /**
     * `--defaults` must report the config keys `--config` actually accepts, which differ
     * per mode: classic configures the Generator, spec the augmenters, and hybrid the
     * augmenters plus the Generator's own `generator.*` keys — it runs no classic processors.
     *
     * @return iterable<mixed>
     */
    public static function defaultsCases(): iterable
    {
        yield 'classic (implicit)' => ['', 'generator', 'operationIds'];
        yield 'classic' => ['-m classic', 'generator', 'operationIds'];
        yield 'hybrid' => ['-m hybrid', 'generator', 'cleanUnusedComponents'];
        yield 'hybrid augmenters' => ['-m hybrid', 'operationIds', 'cleanUnusedComponents'];
        yield 'spec' => ['-m spec', 'operationIds', 'generator'];
    }

    #[DataProvider('defaultsCases')]
    public function testDefaultsAreModeAware(string $args, string $expected, string $unexpected): void
    {
        $basePath = self::examplePath('petstore');
        $cmd = __DIR__ . '/../bin/openapi -D ' . $args . ' ' . escapeshellarg("{$basePath}/annotations");
        exec($this->getCommandToExecute($cmd, '2>'), $output, $retval);

        $this->assertSame(0, $retval, $cmd . PHP_EOL . implode(PHP_EOL, $output));
        $output = implode(PHP_EOL, $output);
        $this->assertStringContainsString($expected, $output);
        $this->assertStringNotContainsString($unexpected, $output);
    }

    /**
     * @return iterable<mixed>
     */
    public static function cleanupConfigCases(): iterable
    {
        yield 'hybrid, default' => ['-m hybrid', false];
        yield 'hybrid, disabled' => ['-m hybrid -c cleanup.enabled=false', true];
    }

    /**
     * The notice `Cleanup` logs names `cleanup.enabled` as the switch, so `-c` has to reach
     * it in every mode that prunes — hybrid included, which routes `-c` to the augmenters.
     */
    #[DataProvider('cleanupConfigCases')]
    public function testCleanupIsConfigurable(string $args, bool $kept): void
    {
        $fixture = __DIR__ . '/Fixtures/UnreferencedSchema.php';
        $cmd = __DIR__ . '/../bin/openapi --bootstrap ' . escapeshellarg($fixture) . " {$args} --format yaml " . escapeshellarg($fixture);
        exec($this->getCommandToExecute($cmd, '2>'), $output, $retval);
        $this->assertSame(0, $retval, $cmd . PHP_EOL . implode(PHP_EOL, $output));

        $output = implode(PHP_EOL, $output);
        if ($kept) {
            $this->assertStringContainsString('UnreferencedSchemaModel', $output);
        } else {
            $this->assertStringNotContainsString('UnreferencedSchemaModel', $output);
        }
    }

    private function getCommandToExecute(string $cmd, ?string $devNullRedir = null): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'php ' . $cmd;
            $devNull = 'NUL';
        } else {
            $devNull = '/dev/null';
        }
        if ($devNullRedir) {
            $cmd .= " {$devNullRedir} {$devNull}";
        }

        return $cmd;
    }
}
