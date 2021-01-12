<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Annotations\OpenApi;
use Symfony\Component\Finder\Finder;

if (defined('OpenApi\\UNDEFINED') === false) {
    /*
     * Special value to differentiate between null and undefined.
     */
    define('OpenApi\\UNDEFINED', Generator::UNDEFINED);
    define('OpenApi\\Annotations\\UNDEFINED', Generator::UNDEFINED);
    define('OpenApi\\Processors\\UNDEFINED', Generator::UNDEFINED);
}

if (!function_exists('OpenApi\\scan')) {
    /**
     * Scan the filesystem for OpenAPI annotations and build openapi-documentation.
     *
     * @param array|Finder|string $directory The directory(s) or filename(s)
     * @param array               $options
     *                                       exclude: string|array $exclude The directory(s) or filename(s) to exclude (as absolute or relative paths)
     *                                       pattern: string       $pattern File pattern(s) to scan (default: *.php)
     *                                       analyser: defaults to StaticAnalyser
     *                                       analysis: defaults to a new Analysis
     *                                       processors: defaults to the registered processors in Analysis
     *                                       logger: PSR Logger
     *
     * @return OpenApi
     */
    function scan($directory, $options = [])
    {
        $logger = array_key_exists('logger', $options) ? $options['logger'] : Logger::psrInstance();
        $analyser = array_key_exists('analyser', $options) ? $options['analyser'] : new StaticAnalyser($logger);
        $analysis = array_key_exists('analysis', $options) ? $options['analysis'] : new Analysis([], null, $logger);
        $processors = array_key_exists('processors', $options) ? $options['processors'] : Analysis::processors($logger);
        $exclude = array_key_exists('exclude', $options) ? $options['exclude'] : null;
        $pattern = array_key_exists('pattern', $options) ? $options['pattern'] : null;

        return (new Generator($logger))
            ->setAnalyser($analyser)
            ->setProcessors($processors)
            ->scan(Util::finder($directory, $exclude, $pattern), true, $analysis);
    }
}
