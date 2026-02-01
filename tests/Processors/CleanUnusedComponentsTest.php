<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Generator;
use OpenApi\Tests\OpenApiTestCase;

final class CleanUnusedComponentsTest extends OpenApiTestCase
{
    public static function countCases(): \Iterator
    {
        $configEnable = ['cleanUnusedComponents' => ['enabled' => true]];
<<<<<<< HEAD

        return [
            'var-default' => [[], 'UsingVar.php', 2, 5],
            'var-clean' => [$configEnable, 'UsingVar.php', 0, 2],
            'unreferenced-default' => [[], 'Unreferenced.php', 2, 11],
            'unreferenced-clean' => [$configEnable, 'Unreferenced.php', 0, 5],
        ];
=======
        yield 'var-default' => [[], 'UsingVar.php', 2, 5];
        yield 'var-clean' => [$configEnable, 'UsingVar.php', 0, 2];
        yield 'unreferenced-default' => [[], 'Unreferenced.php', 2, 14];
        yield 'unreferenced-clean' => [$configEnable, 'Unreferenced.php', 0, 6];
>>>>>>> 09b3543 (Subject examples and tests to rector rules (#1942))
    }

    /**
     * @dataProvider countCases
     */
    public function testCounts(array $config, string $fixture, int $expectedSchemaCount, int $expectedAnnotationCount): void
    {
        $analysis = $this->analysisFromFixtures([$fixture], static::processors(), null, $config);

        if ($expectedSchemaCount === 0) {
            $this->assertTrue(Generator::isDefault($analysis->openapi->components->schemas));
        } else {
            $this->assertCount($expectedSchemaCount, $analysis->openapi->components->schemas);
        }
        $this->assertCount($expectedAnnotationCount, $analysis->annotations);
    }
}
