<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class EncodedPropertyRectorTest extends AbstractRectorTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    /**
     * @return \Iterator<array{string}>
     */
    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/Fixture/EncodedProperty');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/encoded_property.php';
    }
}
