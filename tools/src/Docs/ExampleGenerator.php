<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs;

use OpenApi\Analysers\TokenScanner;
use OpenApi\Annotations\AbstractAnnotation;
use Symfony\Component\Finder\Finder;

class ExampleGenerator extends DocGenerator
{
    public function __construct($projectRoot)
    {
        parent::__construct($projectRoot);
    }

    public function examplePath(string $relativeName): string
    {
        return $this->projectRoot . '/examples/' . $relativeName;
    }

    public function collectFiles(string $folder, string $name): array
    {
        if (!file_exists($folder)) {
            return [];
        }

        $finder = (new Finder())
            ->in($folder)
            ->name($name);

        $files = [];
        foreach ($finder as $file) {
            $relativeName = $file->getRelativePathname();
            $files[$file->getRelativePathname()] = $file->getBasename('.php');
        }

        return $files;
    }
}
