<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Console;

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Annotations\OpenApi;
use OpenApi\Generator;
use OpenApi\Loggers\ConsoleLogger;
use OpenApi\SourceFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected function configure(): void
    {
        $defaultVersion = OpenApi::DEFAULT_VERSION;

        $this
            ->setName('generate')
            ->setDescription('Generate OpenAPI documentation')
            ->addArgument('paths', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Source path(s) to scan')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Generator config (e.g. -c operationId.hash=false)', [])
            ->addOption('defaults', 'D', InputOption::VALUE_NONE, 'Show default config')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Path to store the generated documentation')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Force yaml or json', 'auto')
            ->addOption('exclude', 'e', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude path(s)', [])
            ->addOption('pattern', 'n', InputOption::VALUE_REQUIRED, 'Pattern of files to scan', '*.php')
            ->addOption('bootstrap', 'b', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Bootstrap php file(s) for defining constants, etc.', [])
            ->addOption('add-processor', 'a', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Register an additional processor', [])
            ->addOption('remove-processor', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Remove an existing processor', [])
            ->addOption('version', null, InputOption::VALUE_REQUIRED, "The OpenAPI version; defaults to {$defaultVersion}", $defaultVersion)
            ->addOption('debug', 'd', InputOption::VALUE_NONE, 'Show additional error information')
            ->setHelp(
                <<<'EOF'
Usage: openapi [--option value] [/path/to/project ...]

Options:
  --config (-c)               Generator config.
                              ex: -c operationId.hash=false
  --defaults (-D)             Show default config.
  --output (-o)               Path to store the generated documentation.
                              ex: --output openapi.yaml
  --exclude (-e)              Exclude path(s).
                              ex: --exclude vendor,library/Zend
  --pattern (-n)              Pattern of files to scan.
                              ex: --pattern "*.php" or --pattern "/\.(phps|php)$/"
  --bootstrap (-b)            Bootstrap php file(s) for defining constants, etc.
                              ex: --bootstrap config/constants.php
  --add-processor (-a)        Register an additional processor (allows multiple).
  --remove-processor (-r)     Remove an existing processor (allows multiple).
  --format (-f)               Force yaml or json.
  --debug (-d)                Show additional error information.
  --version                   The OpenAPI version; defaults to <info>{$defaultVersion}</info>.
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $debug = $input->getOption('debug');
        $logger = new ConsoleLogger($debug);

        error_reporting(E_ALL);

        $errorTypes = [
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parser error',
            E_NOTICE => 'Notice',
            E_DEPRECATED => 'Deprecated',
            E_CORE_ERROR => 'Error(Core)',
            E_CORE_WARNING => 'Warning(Core)',
            E_COMPILE_ERROR => 'Error(compile)',
            E_COMPILE_WARNING => 'Warning(Compile)',
            E_RECOVERABLE_ERROR => 'Error(Recoverable)',
            E_USER_ERROR => 'Error',
            E_USER_WARNING => 'Warning',
            E_USER_NOTICE => 'Notice',
            E_USER_DEPRECATED => 'Deprecated',
        ];
        set_error_handler(function ($errno, string|\Stringable $errstr, string $file, string $line) use ($errorTypes, $debug, $logger): void {
            if (!(error_reporting() & $errno)) {
                return;
            }
            $type = array_key_exists($errno, $errorTypes) ? $errorTypes[$errno] : 'Error';
            if ($type === 'Deprecated') {
                $logger->info($errstr, ['prefix' => $type . ': ']);
            } else {
                $logger->error($errstr, ['prefix' => $type . ': ']);
            }

            if ($debug) {
                $logger->info(' in ' . $file . ' on line ' . $line);
            }
            if (str_starts_with($type, 'Error')) {
                exit($errno);
            }
        });

        set_exception_handler(function ($exception) use ($logger): void {
            $logger->error($exception);
            exit($exception->getCode() ?: 1);
        });

        // Bootstrap
        foreach ($input->getOption('bootstrap') as $bootstrap) {
            $filenames = glob($bootstrap);
            if (false === $filenames) {
                $logger->error('Invalid `--bootstrap` value: "' . $bootstrap . '"');

                return 1;
            }
            foreach ($filenames as $filename) {
                if ($debug) {
                    $logger->debug('Bootstrapping: ' . $filename);
                }
                require_once($filename);
            }
        }

        // Defaults
        if ($input->getOption('defaults')) {
            $logger->info('Default config');
            $logger->info(json_encode((new Generator())->getDefaultConfig(), JSON_PRETTY_PRINT));

            return 1;
        }

        // Exclude with comma-deprecation check
        $exclude = $input->getOption('exclude') ?: null;
        if ($exclude && str_contains((string) $exclude[0], ',')) {
            $exploded = explode(',', (string) $exclude[0]);
            $logger->error('Comma-separated exclude paths are deprecated, use multiple --exclude statements: --exclude ' . $exploded[0] . ' --exclude ' . $exploded[1]);
            $exclude[0] = array_shift($exploded);
            $exclude = array_merge($exclude, $exploded);
        }

        // Pattern
        $pattern = $input->getOption('pattern');

        // Generator
        $generator = new Generator($logger);
        foreach ($input->getOption('add-processor') as $processor) {
            $class = '\OpenApi\Processors\\' . ucfirst((string) $processor);
            if (class_exists($class)) {
                $processor = new $class();
            } elseif (class_exists($processor)) {
                $processor = new $processor();
            }
            $generator->getProcessorPipeline()->add($processor);
        }
        foreach ($input->getOption('remove-processor') as $processor) {
            $class = class_exists($processor)
                ? $class
                : '\OpenApi\Processors\\' . ucfirst((string) $processor);
            $generator->getProcessorPipeline()->remove($class);
        }

        // Analyser
        $analyser = new ReflectionAnalyser([
            new AttributeAnnotationFactory(),
            new DocBlockAnnotationFactory(),
        ]);
        $analyser->setGenerator($generator);

        // Generate
        $paths = $input->getArgument('paths');
        $openapi = $generator
            ->setVersion($input->getOption('version'))
            ->setConfig($input->getOption('config'))
            ->setAnalyser($analyser)
            ->generate(new SourceFinder($paths, $exclude, $pattern));

        // Output
        $outputPath = $input->getOption('output');
        if (!$outputPath) {
            if (strtolower((string) $input->getOption('format')) === 'json') {
                echo $openapi->toJson();
            } else {
                echo $openapi->toYaml();
            }
            echo "\n";
        } else {
            if (is_dir($outputPath)) {
                $outputPath .= '/openapi.yaml';
            }
            $openapi->saveAs($outputPath, $input->getOption('format'));
        }

        return $logger->loggedMessageAboveNotice() ? 1 : 0;
    }
}
