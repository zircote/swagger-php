<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Generator;
use OpenApi\Util;

class GeneratorTest extends OpenApiTestCase
{
    const SOURCE_DIR = __DIR__.'/../Examples/swagger-spec/petstore-simple';

    public function sourcesProvider()
    {
        $sourceDir = self::SOURCE_DIR;
        $sources = [
            $sourceDir.'/SimplePet.php',
            $sourceDir.'/SimplePetsController.php',
            $sourceDir.'/api.php',
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
    public function testScan(string $sourceDir, $sources)
    {
        $openapi = (new Generator($this->getLogger()))
            ->scan($sources);

        $this->assertSpecEquals(file_get_contents(sprintf('%s/%s.yaml', $sourceDir, basename($sourceDir))), $openapi);
    }
}
