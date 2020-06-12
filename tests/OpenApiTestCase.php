<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApiTests;

use Closure;
use DirectoryIterator;
use Exception;
use OpenApi\Analysis;
use OpenApi\StaticAnalyser;
use PHPUnit\Framework\TestCase;
use stdClass;
use OpenApi\Analyser;
use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\PathItem;
use OpenApi\Context;
use OpenApi\Logger;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class OpenApiTestCase extends TestCase
{
    protected $countExceptions = 0;

    /**
     * @var array
     */
    private $expectedLogMessages;

    /**
     * @var Closure
     */
    private $originalLogger;

    protected function setUp(): void
    {
        $this->expectedLogMessages = [];
        $this->originalLogger = Logger::getInstance()->log;
        Logger::getInstance()->log = function ($entry, $type) {
            if (count($this->expectedLogMessages)) {
                $assertion = array_shift($this->expectedLogMessages);
                $assertion($entry, $type);
            } else {
                $map = [
                    E_USER_NOTICE => 'notice',
                    E_USER_WARNING => 'warning',
                ];
                if (isset($map[$type])) {
                    $this->fail('Unexpected \OpenApi\Logger::'.$map[$type].'("'.$entry.'")');
                } else {
                    $this->fail('Unexpected \OpenApi\Logger->getInstance()->log("'.$entry.'",'.$type.')');
                }
            }
        };
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->assertCount($this->countExceptions, $this->expectedLogMessages, count($this->expectedLogMessages).' OpenApi\Logger messages were not triggered');
        Logger::getInstance()->log = $this->originalLogger;
        parent::tearDown();
    }

    public function assertOpenApiLogEntryContains($entryPrefix, $message = '')
    {
        $this->expectedLogMessages[] = function ($entry, $type) use ($entryPrefix, $message) {
            if ($entry instanceof Exception) {
                $entry = $entry->getMessage();
            }
            $this->assertStringContainsString($entryPrefix, $entry, $message);
        };
    }

    /**
     * Compare OpenApi specs assuming strings to contain YAML.
     *
     * @param string|array|\stdClass|OpenApi $expected
     * @param string|array|\stdClass|OpenApi $spec
     * @param string $message
     * @param bool $normalized Flag indicating whether the inputs are already normalized or not.
     */
    protected function assertSpecEquals($expected, $spec, $message = '', $normalized = false)
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
                    $this->fail('Invalid YAML: ' . $e->getMessage() . PHP_EOL . $in);
                }
            }

            return $in;
        };

        if (!$normalized) {
            $expected = $normalize($expected);
            $spec = $normalize($spec);
        }

        if (is_iterable($expected) && is_iterable($spec)) {
            foreach ($expected as $key => $value) {
                $this->assertSpecEquals($value, ((array) $spec)[$key], $message, true);
            }
            foreach ($spec as $key => $value) {
                $this->assertSpecEquals(((array) $expected)[$key], $value, $message, true);
            }
        } else {
            $this->assertEquals($expected, $spec, $message);
        }
    }

    /**
     * Parse a comment.
     *
     * @param string $comment Contents of a comment block
     *
     * @return AbstractAnnotation[]
     */
    protected function parseComment($comment)
    {
        $analyser = new Analyser();
        $context = Context::detect(1);

        return $analyser->fromComment("<?php\n/**\n * ".implode("\n * ", explode("\n", $comment))."\n*/", $context);
    }

    /**
     * Create a valid OpenApi object with Info.
     */
    protected function createOpenApiWithInfo()
    {
        return new OpenApi([
            'info' => new Info([
                'title' => 'swagger-php Test-API',
                'version' => 'test',
                '_context' => new Context(['unittest' => true]),
            ]),
            'paths' => [
                new PathItem(['path' => '/test']),
            ],
            '_context' => new Context(['unittest' => true]),
        ]);
    }

    /**
     * Resolve fixture filenames.
     *
     * @param string|array $files One ore more files.
     * @return array Resolved filenames for loading scanning etc.
     */
    public function fixtures($files): array
    {
        return array_map(function ($file) {
            return __DIR__ . '/Fixtures/' . $file;
        }, (array) $files);
    }

    public function analysisFromFixtures($files): Analysis
    {
        $analyser = new StaticAnalyser();
        $analysis = new Analysis();

        foreach ((array) $files as $file) {
            $analysis->addAnalysis($analyser->fromFile($this->fixtures($file)[0]));
        }

        return $analysis;
    }

    public function analysisFromCode(string $code, ?Context $context = null)
    {
        return (new StaticAnalyser())->fromCode("<?php\n" . $code, $context ?: new Context());
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
            if (in_array($class, ['AbstractAnnotation','Operation'])) {
                continue;
            }
            $classes[] = ['OpenApi\\Annotations\\'.$class];
        }

        return $classes;
    }
}
