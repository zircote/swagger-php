<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use DirectoryIterator;
use Exception;
use OpenApi\Analyser;
use OpenApi\Analysis;
use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\PathItem;
use OpenApi\Context;
use OpenApi\StaticAnalyser;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class OpenApiTestCase extends TestCase
{
    public $expectedLogMessages = [];

    protected function setUp(): void
    {
        parent::setUp();

        // reset so processors get new loggers each run
        // Hurray! for static
        foreach (Analysis::processors() as $processor) {
            Analysis::unregisterProcessor($processor);
        }

        $this->expectedLogMessages = [];
    }

    protected function tearDown(): void
    {
        $this->assertEmpty(
            $this->expectedLogMessages,
            implode(PHP_EOL.'  => ', array_merge(
                ['OpenApi\Logger messages were not triggered:'],
                array_map(function (array $value) {
                    return $value[1];
                }, $this->expectedLogMessages)
            ))
        );

        parent::tearDown();
    }

    final public function getLogger(bool $tracking = false): ?LoggerInterface
    {
        if (!$tracking) {
            // allow to test the default behaviour without injected PSR logger
            switch (strtoupper($_ENV['NON_TRACKING_LOGGER'] ?? 'FALLBACK')) {
                case 'NULL':
                    return new NullLogger();
                case 'FALLBACK':
                default:
                    // whatever is set up in Logger::$instance->log
                    return null;
            }
        }

        return new class($this) extends AbstractLogger {
            protected $testCase;

            public function __construct($testCase)
            {
                $this->testCase = $testCase;
            }

            public function log($level, $message, array $context = [])
            {
                if (count($this->testCase->expectedLogMessages)) {
                    list($assertion, $needle) = array_shift($this->testCase->expectedLogMessages);
                    $assertion($message, $level);
                } else {
                    $this->testCase->fail('Unexpected \OpenApi\Logger::'.$level.'("'.$message.'")');
                }
            }
        };
    }

    public function assertOpenApiLogEntryContains($needle, $message = '')
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
     * @param bool                           $normalized flag indicating whether the inputs are already normalized or not
     */
    protected function assertSpecEquals($actual, $expected, $message = '', $normalized = false)
    {
        $normalize = function ($in) {
            if ($in instanceof OpenApi) {
                $in = $in->toYaml();
            }
            if (is_string($in)) {
                // assume YAML
                try {
                    $in = Yaml::parse($in);
                } catch (ParseException $e) {
                    $this->fail('Invalid YAML: '.$e->getMessage().PHP_EOL.$in);
                }
            }

            return $in;
        };

        if (!$normalized) {
            $actual = $normalize($actual);
            $expected = $normalize($expected);
        }

        if (is_iterable($actual) && is_iterable($expected)) {
            foreach ($actual as $key => $value) {
                $this->assertArrayHasKey($key, (array) $expected, $message.': property: "'.$key.'" should be absent, but has value: '.$this->formattedValue($value));
                $this->assertSpecEquals($value, ((array) $expected)[$key], $message.' > '.$key, true);
            }
            foreach ($expected as $key => $value) {
                $this->assertArrayHasKey($key, (array) $actual, $message.': property: "'.$key.'" is missing');
                $this->assertSpecEquals(((array) $actual)[$key], $value, $message.' > '.$key, true);
            }
        } else {
            $this->assertEquals($actual, $expected, $message);
        }
    }

    private function formattedValue($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_numeric($value)) {
            return (string) $value;
        }
        if (is_string($value)) {
            return '"'.$value.'"';
        }
        if (is_object($value)) {
            return get_class($value);
        }

        return gettype($value);
    }

    /**
     * Parse a comment.
     *
     * @param string $comment Contents of a comment block
     *
     * @return AbstractAnnotation[]
     */
    protected function parseComment($comment, ?LoggerInterface $logger = null)
    {
        $logger = $logger ?: $this->getLogger();
        $analyser = new Analyser(null, $logger);
        $context = Context::detect(1, $logger);

        return $analyser->fromComment("<?php\n/**\n * ".implode("\n * ", explode("\n", $comment))."\n*/", $context);
    }

    /**
     * Create a valid OpenApi object with Info.
     */
    protected function createOpenApiWithInfo(?LoggerInterface $logger = null)
    {
        $logger = $logger ?: $this->getLogger();

        return new OpenApi([
            'info' => new Info([
                'title' => 'swagger-php Test-API',
                'version' => 'test',
                '_context' => new Context(['unittest' => true, 'logger' => $logger]),
            ], $logger),
            'paths' => [
                new PathItem(['path' => '/test'], $logger),
            ],
            '_context' => new Context([
                'unittest' => true,
                'logger' => $logger,
            ]),
        ], $logger);
    }

    /**
     * Resolve fixture filenames.
     *
     * @param array|string $files one ore more files
     *
     * @return array resolved filenames for loading scanning etc
     */
    public function fixtures($files): array
    {
        return array_map(function ($file) {
            return __DIR__.'/Fixtures/'.$file;
        }, (array) $files);
    }

    public function analysisFromFixtures($files): Analysis
    {
        $analyser = new StaticAnalyser(null, $this->getLogger());
        $analysis = new Analysis([], null, $this->getLogger());

        foreach ((array) $files as $file) {
            $analysis->addAnalysis($analyser->fromFile($this->fixtures($file)[0]));
        }

        return $analysis;
    }

    public function analysisFromCode(string $code, ?Context $context = null)
    {
        return (new StaticAnalyser(null, $this->getLogger()))
            ->fromCode("<?php\n".$code, $context ?: new Context([
                'logger' => $this->getLogger(),
            ]));
    }

    public function analysisFromDockBlock($comment, ?Context $context = null)
    {
        return (new Analyser(null, $this->getLogger()))
            ->fromComment($comment, $context ?: new Context([
                'logger' => $this->getLogger(),
            ]));
    }

    /**
     * Collect list of all non abstract annotation classes.
     *
     * @return array
     */
    public function allAnnotationClasses()
    {
        $classes = [];
        $dir = new DirectoryIterator(__DIR__.'/../src/Annotations');
        foreach ($dir as $entry) {
            if (!$entry->isFile() || $entry->getExtension() != 'php') {
                continue;
            }
            $class = $entry->getBasename('.php');
            if (in_array($class, ['AbstractAnnotation', 'Operation'])) {
                continue;
            }
            $classes[] = ['OpenApi\\Annotations\\'.$class];
        }

        return $classes;
    }
}
