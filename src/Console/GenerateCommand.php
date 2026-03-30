<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Console;

use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\SourceFinder;
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

        $openapi = $this->generate($input);

        if (!$input->output) {
            if ($input->format->isJson()) {
                echo $openapi->toJson();
            } else {
                echo $openapi->toYaml();
            }
            echo "\n";
        } else {
            $outputPath = $input->output;
            if (is_dir($outputPath)) {
                $outputPath .= '/openapi.yaml';
            }
            $openapi->saveAs($outputPath, $input->format->value);
        }

        return $this->logger->hasErrored() ? 1 : 0;
    }

    private function generate(GenerateInput $input): OA\OpenApi
    {
        $generator = new Generator($this->logger);

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

        return $generator
            ->setVersion($input->version)
            ->setConfig($input->config)
            ->generate(new SourceFinder($input->paths, $input->exclude, $input->pattern));
    }
}
