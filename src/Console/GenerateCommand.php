<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Console;

use OpenApi\Builder;
use OpenApi\Builder\Result;
use OpenApi\Generator;
use OpenApi\Utils\Pipeline;
use OpenApi\Utils\SourceFinder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'openapi',
    description: 'Generate OpenAPI documentation',
)]
class GenerateCommand extends Command
{
    public function __construct(
        private ConsoleLogger $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('paths', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Source path(s) to scan')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Generator/Augmenter config; keys differ per mode, see -D (e.g. -c operationId.hash=false)')
            ->addOption('defaults', 'D', InputOption::VALUE_NONE, 'Show default config')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Path to store the generated documentation (e.g. -o openapi.yaml)')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Force yaml or json', GenerateFormat::AUTO->value)
            ->addOption('exclude', 'e', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude path(s) (e.g. -e vendor -e library/Zend)')
            ->addOption('pattern', 'n', InputOption::VALUE_REQUIRED, 'Pattern of files to scan (e.g. -n "/\.(phps|php)$/")', '*.php')
            ->addOption('bootstrap', 'b', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Bootstrap php file(s) for defining constants, etc. (e.g. -b config/constants.php)')
            ->addOption('add-processor', 'a', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Register an additional processor')
            ->addOption('remove-processor', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Remove an existing processor')
            ->addOption('version', null, InputOption::VALUE_REQUIRED, 'The OpenAPI version')
            ->addOption('mode', 'm', InputOption::VALUE_REQUIRED, 'Set mode classic, hybrid or spec', Builder\Mode::CLASSIC->value)
            ->addOption('debug', 'd', InputOption::VALUE_NONE, 'Show additional error information');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $generateInput = GenerateInput::fromInput($input);

        $io->setVerbosity($generateInput->debug ? OutputInterface::VERBOSITY_DEBUG : $io->getVerbosity());

        foreach ($generateInput->getBootstrapFilenames() as $filename) {
            if ($io->isVerbose()) {
                $io->info('Bootstrapping: ' . $filename);
            }

            require_once $filename;
        }

        if ($generateInput->defaults) {
            $io->title('Default config');
            $io->writeln(json_encode($this->getDefaultConfig($generateInput), JSON_PRETTY_PRINT) ?: '{}');

            return 0;
        }

        $result = $this->generate($generateInput);

        if (!$generateInput->output) {
            if ($generateInput->format->isJson()) {
                echo $result->toJson();
            } else {
                echo $result->toYaml();
            }
            echo "\n";
        } else {
            $outputPath = $generateInput->output;
            if (is_dir($outputPath)) {
                $outputPath .= '/openapi.yaml';
            }
            $result->saveAs($outputPath, $generateInput->format->value);
        }

        return $this->logger->hasErrored() ? 1 : 0;
    }

    /**
     * The config keys `--config` accepts; these differ per mode.
     *
     * Mirrors the routing in {@see self::splitConfig()}: classic configures the Generator,
     * spec the augmenter pipeline, and hybrid both — the Generator only for its own
     * `generator.*` keys, since hybrid runs no classic processors.
     *
     * @return array<string,mixed>
     */
    protected function getDefaultConfig(GenerateInput $input): array
    {
        return match ($input->mode) {
            Builder\Mode::CLASSIC => (new Generator())->getDefaultConfig(),
            Builder\Mode::SPEC => (new Builder())->getAugmenters()->getConfig(),
            Builder\Mode::HYBRID => ['generator' => (new Generator())->getDefaultConfig()['generator']]
                + (new Builder())->getAugmenters()->getConfig(),
        };
    }

    /**
     * Split `-c` into the Generator's share and the augmenters'.
     *
     * Hybrid empties the Generator's processor pipeline, so processor keys would reach
     * nothing there; everything but `generator.*` goes to the augmenters, which do run.
     *
     * @return array{0: array<int|string, mixed>, 1: list<string>} generator, augmenters
     */
    protected function splitConfig(GenerateInput $input): array
    {
        if ($input->mode !== Builder\Mode::HYBRID) {
            return $input->mode === Builder\Mode::SPEC ? [[], $input->config] : [$input->config, []];
        }

        $generator = [];
        $augmenters = [];
        foreach ($input->config as $option) {
            // each -c is a `key=value` string
            if (str_starts_with($option, 'generator.')) {
                $generator[] = $option;
            } else {
                $augmenters[] = $option;
            }
        }

        return [$generator, $augmenters];
    }

    protected function generate(GenerateInput $input): Result
    {
        $builder = (new Builder())
            ->addSource(new SourceFinder($input->paths, $input->exclude, $input->pattern))
            ->setMode($input->mode)
            ->setLogger($this->logger);

        if ($input->version !== null) {
            $builder->setVersion($input->version);
        }

        [$generatorConfig, $augmenterConfig] = $this->splitConfig($input);

        if ($generatorConfig !== [] || $input->addProcessor || $input->removeProcessor) {
            $builder->withGenerator(function (Generator $generator) use ($input, $generatorConfig): void {
                // -c takes `key=value` strings; Generator::setConfig() normalises them
                /** @var array<string, mixed> $config */
                $config = $generatorConfig;
                $generator->setConfig($config);

                foreach ($input->addProcessor as $processor) {
                    $class = '\OpenApi\Processors\\' . ucfirst((string) $processor);
                    if (class_exists($class)) {
                        $processor = new $class();
                    } elseif (class_exists($processor)) {
                        $processor = new $processor();
                    }
                    // neither branch above matching leaves $processor as the raw -a string,
                    // which the pipeline cannot invoke; not validated here, as it never was
                    /** @var callable&object $pipe */
                    $pipe = $processor;
                    $generator->getProcessorPipeline()->add($pipe);
                }

                foreach ($input->removeProcessor as $processor) {
                    $class = class_exists($processor)
                        ? $processor
                        : '\OpenApi\Processors\\' . ucfirst((string) $processor);
                    /** @var class-string $remove */
                    $remove = $class;
                    $generator->getProcessorPipeline()->remove($remove);
                }
            });
        }

        if ($augmenterConfig !== []) {
            $builder->withAugmenters(function (Pipeline $augmenters) use ($augmenterConfig): void {
                $augmenters->configure($augmenterConfig);
            });
        }

        return $builder->build();
    }
}
