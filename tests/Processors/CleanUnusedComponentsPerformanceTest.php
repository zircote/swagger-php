<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Generator;
use OpenApi\Tests\OpenApiTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Performance regression test for CleanUnusedComponents.
 *
 * Run with: phpunit --group performance
 */
#[Group('performance')]
final class CleanUnusedComponentsPerformanceTest extends OpenApiTestCase
{
    private const SCHEMA_COUNT = 300;

    private const UNUSED_RATIO = 0.4;

    private const MAX_CLEANUP_OVERHEAD_RATIO = 1.5;

    public function testCleanupOverheadIsAcceptable(): void
    {
        $sourceDir = $this->generateLargeApi();

        try {
            $timeWithout = $this->measureGeneration($sourceDir, false);
            $timeWith = $this->measureGeneration($sourceDir, true);

            $ratio = $timeWith / $timeWithout;

            $this->assertLessThan(
                self::MAX_CLEANUP_OVERHEAD_RATIO,
                $ratio,
                sprintf(
                    'CleanUnusedComponents overhead is too high: %.1fx (without: %.3fs, with: %.3fs). Max allowed: %.1fx',
                    $ratio,
                    $timeWithout,
                    $timeWith,
                    self::MAX_CLEANUP_OVERHEAD_RATIO
                )
            );
        } finally {
            $this->removeDirectory($sourceDir);
        }
    }

    public function testCleanupRemovesExpectedSchemas(): void
    {
        $sourceDir = $this->generateLargeApi();

        try {
            $usedCount = (int) round(self::SCHEMA_COUNT * (1 - self::UNUSED_RATIO));

            $openapi = (new Generator())
                ->setConfig(['cleanUnusedComponents' => ['enabled' => true]])
                ->generate([$sourceDir]);

            $schemaCount = Generator::isDefault($openapi->components->schemas)
                ? 0
                : count($openapi->components->schemas);

            $this->assertSame($usedCount, $schemaCount, 'All unused schemas should be removed');
        } finally {
            $this->removeDirectory($sourceDir);
        }
    }

    private function measureGeneration(string $sourceDir, bool $cleanup): float
    {
        // Warmup run to eliminate autoloading variance
        (new Generator())
            ->setConfig(['cleanUnusedComponents' => ['enabled' => $cleanup]])
            ->generate([$sourceDir]);

        $start = microtime(true);
        (new Generator())
            ->setConfig(['cleanUnusedComponents' => ['enabled' => $cleanup]])
            ->generate([$sourceDir]);

        return microtime(true) - $start;
    }

    private function generateLargeApi(): string
    {
        $outputDir = sys_get_temp_dir() . '/swagger-php-perf-' . getmypid();
        if (is_dir($outputDir)) {
            $this->removeDirectory($outputDir);
        }

        mkdir($outputDir, 0755, true);
        mkdir($outputDir . '/Models', 0755, true);
        mkdir($outputDir . '/Controllers', 0755, true);

        $schemaCount = self::SCHEMA_COUNT;
        $usedCount = (int) round($schemaCount * (1 - self::UNUSED_RATIO));

        $allSchemas = [];
        for ($i = 0; $i < $schemaCount; $i++) {
            $allSchemas[] = "PerfSchema{$i}";
        }
        $usedSchemas = array_slice($allSchemas, 0, $usedCount);

        // OpenApi info
        file_put_contents($outputDir . '/ApiInfo.php', <<<'PHP'
<?php declare(strict_types=1);

namespace SwaggerPhpPerfTest;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(title: 'Performance Test API', version: '1.0.0'),
)]
class ApiInfo
{
}
PHP);

        // Schema models
        foreach ($allSchemas as $idx => $schemaName) {
            $properties = [
                "        new OA\Property(property: 'id', type: 'integer'),",
                "        new OA\Property(property: 'name', type: 'string'),",
            ];

            // Used schemas cross-reference other used schemas
            if ($idx < $usedCount) {
                $refIdx = ($idx + 1) % $usedCount;
                $refSchema = $usedSchemas[$refIdx];
                $properties[] = "        new OA\Property(property: 'related', ref: '#/components/schemas/{$refSchema}'),";
            }

            $propsStr = implode("\n", $properties);
            file_put_contents($outputDir . "/Models/{$schemaName}.php", <<<PHP
<?php declare(strict_types=1);

namespace SwaggerPhpPerfTest\Models;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: '{$schemaName}',
    type: 'object',
    properties: [
{$propsStr}
    ]
)]
class {$schemaName}
{
}
PHP);
        }

        // Controllers referencing used schemas
        $endpointsPerController = 10;
        $controllerCount = (int) ceil($usedCount / $endpointsPerController);

        for ($c = 0; $c < $controllerCount; $c++) {
            $methods = [];
            for ($e = 0; $e < $endpointsPerController; $e++) {
                $schemaIdx = $c * $endpointsPerController + $e;
                if ($schemaIdx >= $usedCount) {
                    break;
                }
                $schema = $usedSchemas[$schemaIdx];
                $methods[] = <<<PHP
    #[OA\Get(
        path: '/perf/{$schemaIdx}',
        operationId: 'get{$schema}',
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(ref: '#/components/schemas/{$schema}')
            ),
        ]
    )]
    public function get{$schema}(): void
    {
    }
PHP;
            }

            $methodsStr = implode("\n\n", $methods);
            file_put_contents($outputDir . "/Controllers/Controller{$c}.php", <<<PHP
<?php declare(strict_types=1);

namespace SwaggerPhpPerfTest\Controllers;

use OpenApi\Attributes as OA;

class Controller{$c}
{
{$methodsStr}
}
PHP);
        }

        // Register autoloader for the generated namespace
        spl_autoload_register(function (string $class) use ($outputDir): void {
            $prefix = 'SwaggerPhpPerfTest\\';
            if (str_starts_with($class, $prefix)) {
                $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
                $file = $outputDir . '/' . $relative . '.php';
                if (file_exists($file)) {
                    require $file;
                }
            }
        });

        return $outputDir;
    }

    private function removeDirectory(string $dir): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
        }
        rmdir($dir);
    }
}
