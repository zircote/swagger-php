<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\DocBlockParser;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\Tests\Concerns\AssertsSpecEquals;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Concerns\UsesFixtures;
use OpenApi\Type\LegacyTypeResolver;
use OpenApi\Type\TypeInfoTypeResolver;
use OpenApi\TypeResolverInterface;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;

class OpenApiTestCase extends TestCase
{
    use AssertsSpecEquals;
    use ExpectsLogEntries;
    use UsesFixtures;

    /**
     * @param array<string, mixed> $properties
     */
    public function getContext(array $properties = [], ?string $version = OA\OpenApi::DEFAULT_VERSION): Context
    {
        return new Context(
            [
                'version' => $version,
                'logger' => $this->trackingLogger(),
            ] + $properties
        );
    }

    public function getAnalyzer(): AnalyserInterface
    {
        return new ReflectionAnalyser([new DocBlockAnnotationFactory(), new AttributeAnnotationFactory()]);
    }

    public function getTypeResolver(): TypeResolverInterface
    {
        return new TypeInfoTypeResolver();
    }

    /**
     * @return array<string, TypeResolverInterface>
     */
    public static function getTypeResolvers(): array
    {
        return [
            'legacy' => new LegacyTypeResolver(),
            'type-info' => new TypeInfoTypeResolver(),
        ];
    }

    /**
     * @param list<callable&object>|null $processors
     * @param list<class-string>         $strip
     *
     * @return Pipeline<Analysis>
     */
    public function processorPipeline(?array $processors = null, array $strip = []): Pipeline
    {
        $generator = (new Generator())
                ->setTypeResolver($this->getTypeResolver());

        if ($processors) {
            $generator->setProcessorPipeline(new Pipeline($processors));
        }

        return $generator->getProcessorPipeline()
            ->remove(fn ($processor): bool => is_object($processor) && in_array($processor::class, $strip));
    }

    /**
     * @param list<string>            $files
     * @param Pipeline<Analysis>|null $pipeline
     * @param array<string, mixed>    $config
     */
    public function analysisFromFixtures(array $files, ?Pipeline $pipeline = null, ?AnalyserInterface $analyzer = null, array $config = []): Analysis
    {
        $analysis = new Analysis([], $this->getContext());

        (new Generator($this->trackingLogger()))
            ->setConfig($config)
            ->setAnalyser($analyzer ?: $this->getAnalyzer())
            ->setTypeResolver($this->getTypeResolver())
            ->setProcessorPipeline($pipeline ?? new Pipeline())
            ->generate($this->fixtures($files), $analysis, false);

        return $analysis;
    }

    /**
     * Collect a list of all non-abstract annotation classes.
     *
     * @return array<string, array<class-string<OA\AbstractAnnotation>>>
     */
    public static function allAnnotationClasses(): array
    {
        $classes = [];
        $dir = new \DirectoryIterator(__DIR__ . '/../src/Annotations');
        foreach ($dir as $entry) {
            if (!$entry->isFile() || $entry->getExtension() !== 'php') {
                continue;
            }
            $class = $entry->getBasename('.php');
            if (in_array($class, ['AbstractAnnotation', 'JsonSchemaTrait', 'Operation'])) {
                continue;
            }
            /** @var class-string<OA\AbstractAnnotation> $fqdn */
            $fqdn = 'OpenApi\\Annotations\\' . $class;
            $classes[$class] = [$fqdn];
        }

        return $classes;
    }

    /**
     * Collect list of all non-abstract attribute classes.
     */
    /**
     * @return array<string, list<class-string>>
     */
    public static function allAttributeClasses(): array
    {
        $classes = [];
        $dir = new \DirectoryIterator(__DIR__ . '/../src/Attributes');
        foreach ($dir as $entry) {
            if (!$entry->isFile() || $entry->getExtension() !== 'php') {
                continue;
            }
            $class = $entry->getBasename('.php');
            if (in_array($class, ['OperationTrait', 'ParameterTrait'])) {
                continue;
            }
            /** @var class-string $fqdn */
            $fqdn = 'OpenApi\\Attributes\\' . $class;
            $classes[$class] = [$fqdn];
        }

        return $classes;
    }

    #[Before]
    protected function allowClassicDebugNoise(): void
    {
        $this->allowLogEntry('Analysing source:', 'JetBrains');
    }

    /**
     * Create a valid OpenApi object with Info.
     */
    protected function createOpenApiWithInfo(): OA\OpenApi
    {
        return new OA\OpenApi([
            'info' => new OA\Info([
                'title' => 'swagger-php Test-API',
                'version' => 'test',
                '_context' => $this->getContext(),
            ]),
            'paths' => [
                new OA\PathItem(['path' => '/test', '_context' => $this->getContext()]),
            ],
            '_context' => $this->getContext(),
        ]);
    }

    /**
     * @param array<string, string> $extraAliases
     *
     * @return list<OA\AbstractAnnotation>
     */
    protected function annotationsFromDocBlockParser(string $docBlock, array $extraAliases = [], string $version = OA\OpenApi::DEFAULT_VERSION): array
    {
        return (new Generator())
            ->setTypeResolver($this->getTypeResolver())
            ->setVersion($version)
            ->withContext(function (Generator $generator, Analysis $analysis, Context $context) use ($docBlock, $extraAliases): array {
                $docBlockParser = new DocBlockParser($generator->getAliases() + $extraAliases);

                return $docBlockParser->fromComment($docBlock, $this->getContext([], $generator->getVersion()));
            });
    }
}
