<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use DirectoryIterator;
use Exception;
use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\DocBlockParser;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Analysis;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\PathItem;
use OpenApi\Context;
use OpenApi\Analysers\TokenAnalyser;
use OpenApi\Generator;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
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

    public function getTrackingLogger(): ?LoggerInterface
    {
        return new class($this) extends AbstractLogger {
            protected $testCase;

            public function __construct($testCase)
            {
                $this->testCase = $testCase;
            }

            public function log($level, $message, array $context = []): void
            {
                if (count($this->testCase->expectedLogMessages)) {
                    list($assertion, $needle) = array_shift($this->testCase->expectedLogMessages);
                    $assertion($message, $level);
                } else {
                    $this->testCase->fail('Unexpected log line: ' . $level . '("' . $message . '")');
                }
            }
        };
    }

    public function getContext(array $properties = [], ?string $version = OpenApi::DEFAULT_VERSION): Context
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
        $legacyAnalyser = getenv('PHPUNIT_ANALYSER') === 'legacy';

        return $legacyAnalyser
            ? new TokenAnalyser()
            : new ReflectionAnalyser([new DocBlockAnnotationFactory(), new AttributeAnnotationFactory()]);
    }

    public function assertOpenApiLogEntryContains($needle, $message = ''): void
    {
        $this->expectedLogMessages[] = [function ($entry, $type) use ($needle, $message) {
            if ($entry instanceof Exception) {
                $entry = $entry->getMessage();
            }
            $this->assertStringContainsString($needle, $entry, $message);
        }, $needle];
    }

    /**
     * Compare OpenApi specs assuming strings to contain YAML.
     *
     * @param array|OpenApi|\stdClass|string $actual     The generated output
     * @param array|OpenApi|\stdClass|string $expected   The specification
     * @param string                         $message
     * @param bool                           $normalized flag indicating whether the inputs are already normalized or
     *                                                   not
     */
    protected function assertSpecEquals($actual, $expected, string $message = '', bool $normalized = false): void
    {
        $formattedValue = function ($value) {
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
            if ($in instanceof OpenApi) {
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
            $this->assertEquals($actual, $expected, $message);
        }
    }

    /**
     * Create a valid OpenApi object with Info.
     */
    protected function createOpenApiWithInfo(): OpenApi
    {
        return new OpenApi([
            'info' => new Info([
                'title' => 'swagger-php Test-API',
                'version' => 'test',
                '_context' => $this->getContext(),
            ]),
            'paths' => [
                new PathItem(['path' => '/test', '_context' => $this->getContext()]),
            ],
            '_context' => $this->getContext(),
        ]);
    }

    public function example(string $name): string
    {
        return __DIR__ . '/../Examples/' . $name;
    }

    public function fixture(string $file): ?string
    {
        $fixtures = $this->fixtures([$file]);

        return $fixtures ? $fixtures[0] : null;
    }

    /**
     * Resolve fixture filenames.
     *
     * @return array resolved filenames for loading scanning etc
     */
    public function fixtures(array $files): array
    {
        return array_map(function ($file) {
            return __DIR__ . '/Fixtures/' . $file;
        }, $files);
    }

    public function analysisFromFixtures(array $files, array $processors = [], ?AnalyserInterface $analyzer = null): Analysis
    {
        $analysis = new Analysis([], $this->getContext());

        (new Generator($this->getTrackingLogger()))
            ->setAnalyser($analyzer ?: $this->getAnalyzer())
            ->setProcessors($processors)
            ->generate($this->fixtures($files), $analysis, false);

        return $analysis;
    }

    protected function annotationsFromDocBlockParser(string $docBlock, array $extraAliases = [], string $version = OpenApi::DEFAULT_VERSION): array
    {
        return (new Generator())
            ->setVersion($version)
            ->withContext(function (Generator $generator, Analysis $analysis, Context $context) use ($docBlock, $extraAliases) {
                $docBlockParser = new DocBlockParser($generator->getAliases() + $extraAliases);

                return $docBlockParser->fromComment($docBlock, $this->getContext([], $generator->getVersion()));
            });
    }

    /**
     * Collect list of all non-abstract annotation classes.
     *
     * @return array
     */
    public function allAnnotationClasses(): array
    {
        $classes = [];
        $dir = new DirectoryIterator(__DIR__ . '/../src/Annotations');
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
}
