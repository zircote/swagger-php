<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Console;

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Generator;
use OpenApi\SourceFinder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\MapInput;
use Symfony\Component\Console\Logger\ConsoleLogger;

#[AsCommand(
    name: 'generate',
    description: 'Generate OpenAPI documentation',
)]
final class GenerateCommand
{
    public function __construct(
        private ConsoleLogger $logger,
    ) {
    }

    public function __invoke(#[MapInput] GenerateInput $input): int
    {
        // Bootstrap
        foreach ($input->bootstrap as $bootstrapPattern) {
            $filenames = glob($bootstrapPattern);
            if (false === $filenames) {
                $this->logger->error('Invalid `--bootstrap` value: "' . $bootstrapPattern . '"');

                return 1;
            }
            foreach ($filenames as $filename) {
                if ($input->debug) {
                    $this->logger->debug('Bootstrapping: ' . $filename);
                }
                require_once($filename);
            }
        }

        // Defaults
        if ($input->defaults) {
            $this->logger->info('Default config');
            $this->logger->info(json_encode((new Generator())->getDefaultConfig(), JSON_PRETTY_PRINT));

            return 1;
        }

        // Exclude with comma-deprecation check
        $exclude = $input->exclude ?: null;
        if ($exclude && str_contains((string) $exclude[0], ',')) {
            $exploded = explode(',', (string) $exclude[0]);
            $this->logger->error('Comma-separated exclude paths are deprecated, use multiple --exclude statements: --exclude ' . $exploded[0] . ' --exclude ' . $exploded[1]);
            $exclude[0] = array_shift($exploded);
            $exclude = array_merge($exclude, $exploded);
        }

        // Generator
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
        $openapi = $generator
            ->setVersion($input->version)
            ->setConfig($input->config)
            ->setAnalyser($analyser)
            ->generate(new SourceFinder($input->paths, $exclude, $input->pattern));

        // Output
        if (!$input->output) {
            if (strtolower($input->format) === 'json') {
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
            $openapi->saveAs($outputPath, $input->format);
        }

        return $this->logger->hasErrored() ? 1 : 0;
    }
}
