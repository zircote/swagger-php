<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Reference;

use OpenApi\Generator;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\Tools\Docs\DocGenerator;

class ProcessorGenerator extends DocGenerator
{
    public function generate(): array
    {
        $content = $this->renderer->preamble(
            'Processor',
            $this->snippetContent('processors'),
        );

        $content .= $this->renderConfigSection();
        $content .= "\n" . $this->renderer->sectionHeader('Default Processors');

        foreach ($this->collectProcessorsDetails() as $details) {
            $content .= "\n" . $this->renderer->classHeader($details['name'], 'Processors');
            $content .= $this->renderer->classDescription($details['description']);

            if ($details['options']) {
                $configPrefix = lcfirst($details['name']) . '.';
                $content .= "\n" . $this->renderer->processorOptions($details['options'], $configPrefix);
            }

            if ($details['see']) {
                $content .= "\n" . $this->renderer->references($details['see']);
            }
        }

        return ['processors' => $content];
    }

    protected function renderConfigSection(): string
    {
        $out = "\n" . $this->renderer->sectionHeader('Processor Configuration');

        $out .= "\n### Command line\n";
        $out .= <<<'EOT'
The `-c` option takes a name/value pair: the processor name (starting lowercase)
and the option name, separated by a dot (`.`).

To list the available processor names and options use `-D`. It still requires a
source path, e.g. `./vendor/bin/openapi -D src`.

```shell
> ./vendor/bin/openapi -c operationId.hash=true src
> ./vendor/bin/openapi -c pathFilter.tags[]=/pets/ -c pathFilter.tags[]=/store/ src
```

EOT;

        $out .= "\n### Programmatically with PHP\n";

        return $out . <<<'EOT'
Configuration can be set using the `Generator::setConfig()` method. Keys can either be the same
as on the command line or be broken down into nested arrays.

```php
(new Generator())
    ->setConfig([
        'operationId.hash' => true,
        'pathFilter' => [
            'tags' => [
                '/pets/',
                '/store/',
            ],
        ],
    ]);
```

EOT;
    }

    /**
     * @return list<array{name: string, description: string, options: list<array{name: string, type: string, default: string, description: string}>, see: list<string>}>
     */
    protected function collectProcessorsDetails(): array
    {
        $processors = [];
        $defaultProcessors = [];

        (new Generator())
            ->getProcessorPipeline()
            ->walk(function ($processor) use (&$processors, &$defaultProcessors): void {
                $rc = new \ReflectionClass($processor);
                $defaultProcessors[] = $rc->getName();
                $processors[] = $this->collectProcessorData($rc);
            });

        $processorsDir = dirname((new \ReflectionClass(MergeIntoOpenApi::class))->getFileName());
        foreach (glob("{$processorsDir}/*.php") as $processor) {
            $class = 'OpenApi\\Processors\\' . pathinfo($processor, PATHINFO_FILENAME);
            if (!in_array($class, $defaultProcessors)) {
                $rc = new \ReflectionClass($class);
                if (!$rc->isInterface()) {
                    $processors[] = $this->collectProcessorData($rc);
                }
            }
        }

        return $processors;
    }

    protected function collectProcessorData(\ReflectionClass $rc): array
    {
        $classDoc = $this->parseDocblock($rc->getDocComment());

        return [
            'name' => $rc->getShortName(),
            'description' => $classDoc['content'],
            'options' => $this->collectOptions($rc),
            'see' => $classDoc['see'],
        ];
    }
}
