<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Concerns\UsesExamples;
use OpenApi\Utils\SourceFinder;
use OpenApi\Utils\SourceScanner;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SourceScannerTest extends TestCase
{
    use ExpectsLogEntries;
    use UsesExamples;

    public static function sourcesProvider(): iterable
    {
        $sourceDir = self::examplePath('petstore/annotations');

        yield 'dir-string' => [[$sourceDir]];
        yield 'finder' => [new SourceFinder($sourceDir)];
        yield 'finder-list' => [[new SourceFinder($sourceDir)]];
    }

    #[DataProvider('sourcesProvider')]
    public function testScan(iterable $sources): void
    {
        $scanner = new SourceScanner($this->trackingLogger());
        $files = $scanner->scan($sources);

        $this->assertNotEmpty($files);
        foreach ($files as $file) {
            $this->assertFileExists($file);
            $this->assertStringEndsWith('.php', $file);
        }
    }

    public function testScanInvalidSource(): void
    {
        $this->expectLogEntry('Skipping invalid source: /tmp/__swagger_php_does_not_exist__');

        $scanner = new SourceScanner($this->trackingLogger());
        $files = $scanner->scan(['/tmp/__swagger_php_does_not_exist__']);

        $this->assertEmpty($files);
    }

    public function testScanNestedIterables(): void
    {
        $sourceDir = self::examplePath('petstore/annotations');
        $nested = [new SourceFinder($sourceDir)];

        $scanner = new SourceScanner($this->trackingLogger());
        $files = $scanner->scan($nested);

        $this->assertNotEmpty($files);
    }

    public function testScanSplFileInfo(): void
    {
        $sourceDir = self::examplePath('petstore/annotations');
        $finder = new SourceFinder($sourceDir);
        $splFiles = iterator_to_array($finder);
        $first = reset($splFiles);

        $scanner = new SourceScanner($this->trackingLogger());
        $files = $scanner->scan([$first]);

        $this->assertCount(1, $files);
        $this->assertFileExists($files[0]);
    }

    public function testScanReflectors(): void
    {
        $reflector = new \ReflectionClass(self::class);

        $scanner = new SourceScanner($this->trackingLogger());
        $files = $scanner->scan([$reflector]);

        $this->assertEmpty($files);
        $this->assertCount(1, $scanner->getReflectors());
        $this->assertSame($reflector, $scanner->getReflectors()[0]);
    }

    public function testScanMixed(): void
    {
        $sourceDir = self::examplePath('petstore/annotations');
        $reflector = new \ReflectionClass(self::class);

        $scanner = new SourceScanner($this->trackingLogger());
        $files = $scanner->scan([$sourceDir, $reflector]);

        $this->assertNotEmpty($files);
        $this->assertCount(1, $scanner->getReflectors());
        $this->assertSame($reflector, $scanner->getReflectors()[0]);
    }
}
