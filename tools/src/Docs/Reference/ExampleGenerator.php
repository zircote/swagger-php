<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Reference;

use OpenApi\Tools\Docs\DocGenerator;
use Symfony\Component\Finder\Finder;

class ExampleGenerator extends DocGenerator
{
    protected array $examples = ['api', 'petstore', 'polymorphism'];

    public function generate(): array
    {
        $content = $this->renderer->preamble('Example', $this->snippetContent('example'));

        foreach ($this->examples as $name) {
            $exampleFolder = $this->examplePath("specs/{$name}");
            $readme = file_exists("{$exampleFolder}/Readme.md")
                ? file_get_contents("{$exampleFolder}/Readme.md")
                : null;

            $files = $this->collectExampleFiles($name);
            $content .= "\n" . $this->renderer->exampleSection($name, $readme, $files);
        }

        return ['examples' => $content];
    }

    public function examplePath(string $relativeName): string
    {
        return $this->projectRoot . '/docs/examples/' . $relativeName;
    }

    /**
     * @return array<string, array{basename: string, attributes: bool}>
     */
    protected function collectExampleFiles(string $name): array
    {
        $typeFiles = [];
        foreach (['annotations', 'attributes'] as $type) {
            $typeFolder = $this->examplePath("specs/{$name}/{$type}");
            $typeFiles[$type] = $this->collectFiles($typeFolder, '*.php');
        }

        $files = [];
        foreach ($typeFiles['annotations'] as $relFilename => $basename) {
            $files[$relFilename] = [
                'basename' => $basename,
                'attributes' => array_key_exists($relFilename, $typeFiles['attributes']),
            ];
        }

        return $files;
    }

    /**
     * @return array<string, string>
     */
    protected function collectFiles(string $folder, string $name): array
    {
        if (!file_exists($folder)) {
            return [];
        }

        $finder = (new Finder())
            ->in($folder)
            ->name($name)
            ->sortByName();

        $files = [];
        foreach ($finder as $file) {
            $files[$file->getRelativePathname()] = $file->getBasename('.php');
        }

        return $files;
    }
}
