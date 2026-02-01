<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Attributes\OpenApi;
use OpenApi\Generator;
use OpenApi\Pipeline;
use OpenApi\Processors\OperationId;
use OpenApi\Tests\Concerns\UsesExamples;
use Symfony\Component\Finder\Finder;

<<<<<<< HEAD
/**
 * @requires PHP 8.1
 */
class DocSnippetsTest extends OpenApiTestCase
=======
final class DocSnippetsTest extends OpenApiTestCase
>>>>>>> 09b3543 (Subject examples and tests to rector rules (#1942))
{
    use UsesExamples;

    public function snippetSets(): iterable
    {
        $finder = (new Finder())
            ->in(__DIR__ . '/../docs/snippets/')
            ->name('*.php');

        foreach ($finder as $file) {
            if (str_ends_with($file->getPathname(), '_an.php')) {
                $other = str_replace('_an.php', '_at.php', $file->getPathname());
                if (file_exists($other)) {
                    $key = str_replace('_an', '', $file->getBasename('.php'));
                    foreach ([OpenApi::VERSION_3_0_0, OpenApi::VERSION_3_1_0] as $version) {
<<<<<<< HEAD
                        yield "$key-$version" => [[$file->getPathname(), $other], $version];
=======
                        yield "{$key}-{$version}" => [['an' => $file->getPathname(), 'at' => $other], $version];
>>>>>>> 09b3543 (Subject examples and tests to rector rules (#1942))
                    }
                }
            }
        }
    }

    /**
     * Compare at/an snippets and ensure they result in the same spec fragment.
     *
     * @dataProvider snippetSets
     */
    public function testSnippets(array $filenames, string $version): void
    {
        $lastSpec = null;
<<<<<<< HEAD
        foreach ($filenames as $filename) {
            $namespace = basename($filename, '.php');
            $tmp = sys_get_temp_dir() . "/$namespace.php";
            file_put_contents($tmp, "<?php namespace $namespace; ?>" . file_get_contents($filename));
=======
        foreach ($filenames as $type => $filename) {
            $contents = preg_replace('/(namespace [^;]+);/', "\\1\\{$type};", file_get_contents($filename));
            $namespace = basename((string) $filename, '.php');

            $tmp = sys_get_temp_dir() . "/{$namespace}.php";
            file_put_contents($tmp, $contents);
>>>>>>> 09b3543 (Subject examples and tests to rector rules (#1942))
            require_once $tmp;
            $openapi = (new Generator($this->getTrackingLogger()))
                ->setVersion($version)
                ->setTypeResolver($this->getTypeResolver())
                 ->withProcessorPipeline(fn (Pipeline $processorPipeline): Pipeline => $processorPipeline->remove(OperationId::class))
                ->generate([$tmp], null, false);
            if ($lastSpec) {
                $this->assertSpecEquals(
                    $openapi,
                    $lastSpec,
                    "Snippet: \${$filename}"
                );

            }
            $lastSpec = $openapi;
        }
    }
}
