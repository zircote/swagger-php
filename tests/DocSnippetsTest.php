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

    public static function snippetSets(): iterable
    {
        $finder = (new Finder())
            ->in(__DIR__ . '/../docs/snippets/')
            ->name('*_an.php');

        foreach ($finder as $file) {
            $key = str_replace('_an', '', $file->getBasename('.php'));
            $snippet_spec = str_replace('_an.php', '-3.1.0.yaml', $file->getPathname());

            foreach (['an', 'at', 'spec'] as $implementation) {
                $snippet = str_replace('_an.php', "_{$implementation}.php", $file->getPathname());
                foreach ([Mode::CLASSIC, Mode::HYBRID, Mode::SPEC] as $mode) {
                    if ($mode === Mode::SPEC && $implementation !== 'spec'
                        || $mode === Mode::CLASSIC && $implementation === 'spec'
                        || !file_exists($snippet)
                    ) {
                        continue;
                    }

                    yield "{$key}-{$implementation}-$mode->value" => [
                        $snippet,
                        $implementation,
                        $mode,
                        $snippet_spec,
                        ];
                }
            }
        }
    }

    /**
     * Compare snippets and ensure they result in the same spec fragment.
     */
    #[DataProvider('snippetSets')]
    public function testSnippets(string $snippet, string $implementation, Mode $mode, string $spec): void
    {
        // normalize and make namespace unique
        $contents = preg_replace('/(namespace [^;]+);/', "\\1\\{$implementation};", file_get_contents($snippet));
        $namespace = basename($snippet, '.php');

        // write to file so we can load it for reflection to work
        $tmp = sys_get_temp_dir() . "/{$namespace}.php";
        file_put_contents($tmp, $contents);
        require_once $tmp;

        $result = (new Builder())
            ->setMode($mode)
            ->setVersion('3.1.0')
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
        $this->assertSpecEquals(file_get_contents($spec), $result->toArray());
    }
}
