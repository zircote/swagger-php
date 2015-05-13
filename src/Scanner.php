<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Exception;
use Swagger\Annotations\Swagger;
use Swagger\Processors\BuildPaths;
use Swagger\Processors\MergeSwagger;
use Symfony\Component\Finder\Finder;

/**
 * Scanner extracts extracts swagger-php annotations from given locations.
 */
class Scanner
{
    /** @var AnalyserInterface */
    private $analyser;
    /** @var callable[] */
    private $processors;
    /** @var bool */
    private $validate;

    public function __construct(AnalyserInterface $analyser, array $processors, $validate = true)
    {
        $this->analyser   = $analyser;
        $this->processors = $processors;
        $this->validate   = $validate;
    }

    /**
     * Create a new scanner using the reflecting analyser and default processor configuration.
     *
     * @return static
     */
    public static function createReflecting()
    {
        return new static(ReflectingAnalyser::createDefault(), [
            new MergeSwagger(),
            new BuildPaths(),
        ]);
    }

    /**
     * Read annotations from given locations and build a Swagger object.
     *
     * @param string|array|Finder $directory
     * @param array|string        $exclude
     *
     * @return Swagger
     * @throws Exception
     */
    public function scan($directory, $exclude = [])
    {
        $finder  = buildFinder($directory, $exclude);
        $swagger = new Swagger([]);

        foreach ($this->getAnnotations($finder) as $fileAnnotations) {
            $swagger->merge($fileAnnotations);
        }

        foreach ($this->processors as $processor) {
            $processor($swagger);
        }

        if ($this->validate) {
            $swagger->validate();
        }

        return $swagger;
    }

    private function getAnnotations(Finder $finder)
    {
        $annotations = [];
        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            $annotations[] = $this->analyser->fromFile($file->getPathname());
        }

        return $annotations;
    }
}
