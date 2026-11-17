<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Augmenter\Cleanup;
use OpenApi\Augmenter\OperationIds;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Generator;
use OpenApi\Processors\OperationId;
use OpenApi\Tests\Concerns\UsesExamples;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Finder\Finder;

final class DocSnippetsTest extends OpenApiTestCase
{
    use UsesExamples;

    /**
     * @return iterable<mixed>
     */
    public static function snippetSets(): iterable
    {
        $finder = (new Finder())
            ->in(__DIR__ . '/../docs/snippets/')
            ->name(['*_an.php', '*_spec.php']);

        foreach ($finder as $file) {
            // a set is found by its `_an` file; a `_spec` file stands alone only without one
            $basename = $file->getBasename('.php');
            $key = (string) preg_replace('/_(an|spec)$/', '', $basename);
            $basePath = dirname($file->getPathname());
            if (str_ends_with($basename, '_spec') && file_exists("{$basePath}/{$key}_an.php")) {
                continue;
            }

            foreach (['an', 'at', 'spec'] as $implementation) {
                $snippet = "{$basePath}/{$key}_{$implementation}.php";
                foreach ([Mode::CLASSIC, Mode::HYBRID, Mode::SPEC] as $mode) {
                    if ($mode === Mode::SPEC && $implementation !== 'spec'
                        || $mode === Mode::CLASSIC && $implementation === 'spec'
                        || !file_exists($snippet)
                    ) {
                        continue;
                    }

                    // a snippet using what only a later version has is checked against that version
                    foreach (['3.1.0', '3.2.0'] as $version) {
                        $candidates = [
                            "{$basePath}/{$key}_{$implementation}-{$version}.yaml",
                            "{$basePath}/{$key}-{$mode->value}-{$version}.yaml",
                        ];
                        if ($mode === Mode::HYBRID) {
                            $candidates[] = "{$basePath}/{$key}-spec-{$version}.yaml";
                        }
                        $candidates[] = "{$basePath}/{$key}-{$version}.yaml";

                        $snippet_spec = null;
                        foreach ($candidates as $candidate) {
                            if (file_exists($candidate)) {
                                $snippet_spec = $candidate;
                                break;
                            }
                        }

                        if ($snippet_spec === null) {
                            continue;
                        }

                        yield "{$key}-{$implementation}-$mode->value" . ($version !== '3.1.0' ? "-{$version}" : '') => [
                            $snippet,
                            $implementation,
                            $mode,
                            $snippet_spec,
                            $version,
                        ];
                    }
                }
            }
        }
    }

    /**
     * Compare snippets and ensure they result in the same spec fragment.
     */
    #[DataProvider('snippetSets')]
    public function testSnippets(string $snippet, string $implementation, Mode $mode, string $spec, string $version): void
    {
        // normalize and make namespace unique
        $source = file_get_contents($snippet);
        $this->assertIsString($source, "Could not read {$snippet}");
        $contents = preg_replace('/(namespace [^;]+);/', "\\1\\{$implementation};", $source);
        $namespace = basename($snippet, '.php');

        // write to file so we can load it for reflection to work
        $tmp = sys_get_temp_dir() . "/{$namespace}.php";
        file_put_contents($tmp, $contents);
        require_once $tmp;

        $result = (new Builder())
            ->setMode($mode)
            ->setVersion($version)
            ->setSources([$tmp])
            ->withGenerator(function (Generator $generator): void {
                $generator
                ->setTypeResolver($this->getTypeResolver())
                ->withProcessorPipeline(
                    fn (Pipeline $processorPipeline): Pipeline => $processorPipeline
                    ->remove(OperationId::class)
                );
            })
            ->withAugmenters(
                fn (Pipeline $augmenters): Pipeline => $augmenters
                ->remove(OperationIds::class)
                ->remove(Cleanup::class)
            )
            ->build();

        // file_put_contents($spec, $result->toYaml());
        $this->assertSpecEquals($result->toYaml(), file_get_contents($spec));
    }
}
