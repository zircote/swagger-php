<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Console;

use OpenApi\Builder;
use OpenApi\Builder\Result;
use OpenApi\Generator;
use OpenApi\Utils\SourceFinder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\MapInput;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'openapi',
    description: 'Generate OpenAPI documentation',
)]
class GenerateCommand
{
    public function __construct(
        private ConsoleLogger $logger,
    ) {
    }

    public function __invoke(#[MapInput] GenerateInput $input, SymfonyStyle $io): int
    {
        $io->setVerbosity($input->debug ? OutputInterface::VERBOSITY_DEBUG : $io->getVerbosity());

        foreach ($input->getBootstrapFilenames() as $filename) {
            if ($io->isVerbose()) {
                $io->info('Bootstrapping: ' . $filename);
            }

            require_once($filename);
        }

        if ($input->defaults) {
            $io->title('Default config');
            $io->writeln(json_encode((new Generator())->getDefaultConfig(), JSON_PRETTY_PRINT));

            return 0;
        }

        $result = $this->generate($input);

        if (!$input->output) {
            if ($input->format->isJson()) {
                echo $result->toJson();
            } else {
                echo $result->toYaml();
            }
            echo "\n";
        } else {
            $outputPath = $input->output;
            if (is_dir($outputPath)) {
                $outputPath .= '/openapi.yaml';
            }
            $result->saveAs($outputPath, $input->format->value);
        }

        return $this->logger->hasErrored() ? 1 : 0;
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

        if ($input->config || $input->addProcessor || $input->removeProcessor) {
            $builder->withGenerator(function (Generator $generator) use ($input): void {
                if ($input->config) {
                    $generator->setConfig($input->config);
                }

                foreach ($input->addProcessor as $processor) {
                    $class = '\OpenApi\Processors\\' . ucfirst((string) $processor);
                    if (class_exists($class)) {
                        $processor = new $class();
                    } elseif (class_exists($processor)) {
                        $processor = new $processor();
                    }
                    $generator->getProcessorPipeline()->add($processor);
                }

                foreach ($input->removeProcessor as $processor) {
                    $class = class_exists($processor)
                        ? $processor
                        : '\OpenApi\Processors\\' . ucfirst((string) $processor);
                    $generator->getProcessorPipeline()->remove($class);
                }
            });
        }

        return $builder->build();
    }
}
