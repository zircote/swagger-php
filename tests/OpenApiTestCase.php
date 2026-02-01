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
use OpenApi\GeneratorAwareInterface;
use OpenApi\Pipeline;
use OpenApi\Type\LegacyTypeResolver;
use OpenApi\Type\TypeInfoTypeResolver;
use OpenApi\TypeResolverInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class OpenApiTestCase extends TestCase
{
    /**
     * @var array
     */
    public $expectedLogMessages = [];

    protected function setUp(): void
    {
        $this->expectedLogMessages = [];

        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->assertEmpty(
            $this->expectedLogMessages,
            implode(PHP_EOL . '  => ', array_merge(
                ['OpenApi\Logger messages were not triggered:'],
                array_map(function (array $value) {
                    return $value[1];
                }, $this->expectedLogMessages)
            ))
        );

        parent::tearDown();
    }

    public function getTrackingLogger(bool $debug = false): ?LoggerInterface
    {
        return new class ($this, $debug) extends AbstractLogger {
            /** @var OpenApiTestCase */
            protected $testCase;

            protected $debug;

            public function __construct(OpenApiTestCase $testCase, bool $debug = false)
            {
                $this->testCase = $testCase;
                $this->debug = $debug;
            }

            public function log($level, $message, array $context = []): void
            {
                if (LogLevel::DEBUG == $level) {
                    if (!$this->debug || 0 === strpos($message, 'Analysing source:')) {
                        return;
                    }
                }

                if (count($this->testCase->expectedLogMessages)) {
                    list($assertion, $needle) = array_shift($this->testCase->expectedLogMessages);
                    $assertion($message, $level);
                } else {
                    $this->testCase->fail('Unexpected log line: ' . $level . '("' . $message . '")');
                }
            }
        };
    }

    public function getContext(array $properties = [], ?string $version = OA\OpenApi::DEFAULT_VERSION): Context
    {
        return new Context(
            [
                'version' => $version,
                'logger' => $this->getTrackingLogger(),
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

    public static function getTypeResolvers(): array
    {
        return [
            'legacy' => new LegacyTypeResolver(),
            'type-info' => new TypeInfoTypeResolver(),
        ];
    }

    public function initializeProcessors(array $processors): array
    {
        $generator = (new Generator())
            ->setTypeResolver($this->getTypeResolver());

        array_walk($processors, function ($processor) use ($generator) {
            if (is_a($processor, GeneratorAwareInterface::class)) {
                $processor->setGenerator($generator);
            }
        });

        return $processors;
    }

    public function assertOpenApiLogEntryContains(string $needle, string $message = ''): void
    {
        $this->expectedLogMessages[] = [function ($entry, $type) use ($needle, $message): void {
            if ($entry instanceof \Exception) {
                $entry = $entry->getMessage();
            }
            $this->assertStringContainsString($needle, $entry, $message);
        }, $needle];
    }

    /**
     * Compare OpenApi specs assuming strings to contain YAML.
     *
     * @param array|OA\OpenApi|\stdClass|string $actual     The generated output
     * @param array|OA\OpenApi|\stdClass|string $expected   The specification
     * @param bool                              $normalized flag indicating whether the inputs are already normalized or
     *                                                      not
     */
    public function assertSpecEquals($actual, $expected, string $message = '', bool $normalized = false): void
    {
        $formattedValue = function ($value): string {
            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }
            if (is_numeric($value)) {
                return (string) $value;
            }
            if (is_string($value)) {
                return '"' . $value . '"';
            }
            if (is_object($value)) {
                return get_class($value);
            }

            return gettype($value);
        };

        $normalizeIn = function ($in) {
            if ($in instanceof OA\OpenApi) {
                $in = $in->toYaml();
            }

            if (is_string($in)) {
                // assume YAML
                try {
                    $in = Yaml::parse($in);
                } catch (ParseException $e) {
                    $this->fail('Invalid YAML: ' . $e->getMessage() . PHP_EOL . $in);
                }
            }

            return $in;
        };

        if (!$normalized) {
            $actual = $normalizeIn($actual);
            $expected = $normalizeIn($expected);
        }

        if (is_iterable($actual) && is_iterable($expected)) {
            foreach ($actual as $key => $value) {
                $this->assertArrayHasKey($key, (array) $expected, $message . ': property: "' . $key . '" should be absent, but has value: ' . $formattedValue($value));
                $this->assertSpecEquals($value, ((array) $expected)[$key], $message . ' > ' . $key, true);
            }
            foreach ($expected as $key => $value) {
                $this->assertArrayHasKey($key, (array) $actual, $message . ': property: "' . $key . '" is missing');
                $this->assertSpecEquals(((array) $actual)[$key], $value, $message . ' > ' . $key, true);
            }
        } else {
            $this->assertEquals($expected, $actual, $message);
        }
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

    public static function fixture(string $file): ?string
    {
        $fixtures = static::fixtures([$file]);

        return $fixtures ? $fixtures[0] : null;
    }

    /**
     * Resolve fixture filenames.
     *
     * @return array resolved filenames for loading scanning etc
     */
    public static function fixtures(array $files): array
    {
        return array_map(function ($file) {
            return __DIR__ . '/Fixtures/' . $file;
        }, $files);
    }

    public static function processors(array $strip = []): array
    {
        $processors = [];

        (new Generator())
            ->getProcessorPipeline()
            ->walk(function ($processor) use (&$processors, $strip) {
                if (!is_object($processor) || !in_array(get_class($processor), $strip)) {
                    $processors[] = $processor;
                }
            });

        return $processors;
    }

    public function analysisFromFixtures(array $files, array $processors = [], ?AnalyserInterface $analyzer = null, array $config = []): Analysis
    {
        $analysis = new Analysis([], $this->getContext());

        (new Generator($this->getTrackingLogger()))
            ->setConfig($config)
            ->setAnalyser($analyzer ?: $this->getAnalyzer())
            ->setTypeResolver($this->getTypeResolver())
            ->setProcessorPipeline(new Pipeline($processors))
            ->generate($this->fixtures($files), $analysis, false);

        return $analysis;
    }

    protected function annotationsFromDocBlockParser(string $docBlock, array $extraAliases = [], string $version = OA\OpenApi::DEFAULT_VERSION): array
    {
        return (new Generator())
            ->setTypeResolver($this->getTypeResolver())
            ->setVersion($version)
            ->withContext(function (Generator $generator, Analysis $analysis, Context $context) use ($docBlock, $extraAliases) {
                $docBlockParser = new DocBlockParser($generator->getAliases() + $extraAliases);

                return $docBlockParser->fromComment($docBlock, $this->getContext([], $generator->getVersion()));
            });
    }

    /**
     * Collect list of all non-abstract annotation classes.
     */
    public static function allAnnotationClasses(): array
    {
        $classes = [];
        $dir = new \DirectoryIterator(__DIR__ . '/../src/Annotations');
        foreach ($dir as $entry) {
            if (!$entry->isFile() || $entry->getExtension() != 'php') {
                continue;
            }
            $class = $entry->getBasename('.php');
            if (in_array($class, ['AbstractAnnotation', 'Operation'])) {
                continue;
            }
            $classes[$class] = ['OpenApi\\Annotations\\' . $class];
        }

        return $classes;
    }

    /**
     * Collect list of all non-abstract attribute classes.
     */
    public static function allAttributeClasses(): array
    {
        $classes = [];
        $dir = new \DirectoryIterator(__DIR__ . '/../src/Attributes');
        foreach ($dir as $entry) {
            if (!$entry->isFile() || $entry->getExtension() != 'php') {
                continue;
            }
            $class = $entry->getBasename('.php');
            if (in_array($class, ['OperationTrait', 'ParameterTrait'])) {
                continue;
            }
            $classes[$class] = ['OpenApi\\Attributes\\' . $class];
        }

        return $classes;
    }
}
