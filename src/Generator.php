<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use Doctrine\Common\Annotations\AnnotationRegistry;
use OpenApi\Analyser\DocBlockParser;
use OpenApi\Annotations\OpenApi;
use Psr\Log\LoggerInterface;

/**
 * OpenApi spec generator.
 *
 * Scans PHP source code and generates OpenApi specifications from the found OpenApi annotations.
 *
 * This is an object oriented alternative to using the `\OpenApi\scan()` function and static class properties
 * of the `Analyzer` and `Analysis` classes.
 *
 * The `aliases` property supersedes the `Analyser::$defaultImports`; `namespaces` maps to `Analysis::$whitelist`.
 */
class Generator
{
    /** @var string Special value to differentiate between null and undefined. */
    public const UNDEFINED = '@OA\UNDEFINED🙈';

    /** @var array Map of namespace aliases to be supported by doctrine. */
    protected $aliases = null;

    /** @var array List of annotation namespaces to be autoloaded by doctrine. */
    protected $namespaces = null;

    /** @var StaticAnalyser The configured analyzer. */
    protected $analyser;

    /** @var null|callable[] List of configured processors. */
    protected $processors = null;

    /** @var LoggerInterface The configured logger. */
    protected $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: Logger::psrInstance();

        if (class_exists(AnnotationRegistry::class, true)) {
            $self = $this;
            AnnotationRegistry::registerLoader(
                function (string $class) use ($self): bool {
                    foreach ($self->getNamespaces() as $namespace) {
                        if (strtolower(substr($class, 0, strlen($namespace))) === strtolower($namespace)) {
                            $loaded = class_exists($class);
                            if (!$loaded && $namespace === 'OpenApi\\Annotations\\') {
                                if (in_array(strtolower(substr($class, 20)), ['definition', 'path'])) {
                                    // Detected an 2.x annotation?
                                    throw new OpenApiException('The annotation @SWG\\'.substr($class, 20).'() is deprecated. Found in '.Analyser::$context."\nFor more information read the migration guide: https://github.com/zircote/swagger-php/blob/master/docs/Migrating-to-v3.md");
                                }
                            }

                            return $loaded;
                        }
                    }

                    return false;
                }
            );
        }
    }

    public function getAliases(): array
    {
        $aliases = null !== $this->aliases ? $this->aliases : Analyser::$defaultImports;
        $aliases['oa'] = 'OpenApi\\Annotations';

        return $aliases;
    }

    public function setAliases(array $aliases): Generator
    {
        $this->aliases = $aliases;

        return $this;
    }

    public function getNamespaces(): array
    {
        $namespaces = null !== $this->namespaces ? $this->namespaces : Analyser::$whitelist;
        $namespaces = false !== $namespaces ? $namespaces : [];
        $namespaces[] = 'OpenApi\\Annotations\\';

        return $namespaces;
    }

    public function setNamespaces(array $namespaces): Generator
    {
        $this->namespaces = $namespaces;

        return $this;
    }

    public function getAnalyser(): StaticAnalyser
    {
        return $this->analyser ?: new StaticAnalyser(new DocBlockParser($this->getAliases(), $this->logger), $this->logger);
    }

    public function setAnalyser(StaticAnalyser $analyser): Generator
    {
        $this->analyser = $analyser;

        return $this;
    }

    /**
     * @return callable[]
     */
    public function getProcessors(): array
    {
        return null !== $this->processors ? $this->processors : Analysis::processors($this->logger);
    }

    /**
     * @param null|callable[] $processors
     */
    public function setProcessors(?array $processors): Generator
    {
        $this->processors = $processors;

        return $this;
    }

    /**
     * Scan the given source files.
     *
     * @param iterable $sources filenames (`string`) or \SplFileInfo
     */
    public function scan(iterable $sources, ?Analysis $analysis = null, bool $validate = true): OpenApi
    {
        $analysis = $analysis ?: new Analysis([], null, $this->logger);

        $this->scanSources($sources, $analysis);

        // post processing
        $analysis->process($this->getProcessors());

        // validation
        if ($validate) {
            $analysis->validate();
        }

        return $analysis->openapi;
    }

    protected function scanSources(iterable $sources, Analysis $analysis): void
    {
        $analyser = $this->getAnalyser();
        foreach ($sources as $source) {
            if (is_iterable($source)) {
                $this->scanSources($source, $analysis);
            } else {
                $source = $source instanceof \SplFileInfo ? $source->getPathname() : realpath($source);
                if (is_dir($source)) {
                    $this->scanSources(Util::finder($source), $analysis);
                } else {
                    $analysis->addAnalysis($analyser->fromFile($source));
                }
            }
        }
    }
}
