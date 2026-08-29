<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Reference;

use OpenApi\Builder;
use OpenApi\Tools\Docs\DocGenerator;

class AugmenterGenerator extends DocGenerator
{
    public function generate(): array
    {
        $content = $this->renderer->preamble(
            'Augmenter',
            $this->snippetContent('augmenters'),
        );

        $content .= $this->renderConfigSection();
        $content .= "\n" . $this->renderer->sectionHeader('Default Augmenters');

        foreach ($this->collectAugmenterDetails() as $data) {
            $content .= "\n" . $this->renderer->classHeader($data['name'], 'Augmenter');
            $content .= $this->renderSections($data);
        }

        return ['augmenters' => $content];
    }

    protected function renderConfigSection(): string
    {
        return parent::renderConfiguration(
            'augmenter',
            '--mode spec',
            [
                '-c operationIds.hash=true src',
                '-c pathFilter.tags[]=/pets/ -c pathFilter.tags[]=/store/ src',
            ],
            <<<'EOT'
                Configuration can be set using the `Builder::withAugmenters()` method to access the pipeline
                and configure individual augmenters via `Pipeline::get()`.

                ```php
                (new Builder())
                    ->withAugmenters(function ($pipeline) {
                        $pipeline->get(Augmenter\OperationIds::class)->setHash(true);
                        $pipeline->get(Augmenter\PathFilter::class)->setTags(['/pets/', '/store/']);
                    });
                ```

                EOT,
        );
    }

    /**
     * @return list<array{name: string, description: string, configPrefix: string, options: list<array{name: string, type: string, default: string, description: string}>, see: list<string>}>
     */
    protected function collectAugmenterDetails(): array
    {
        $augmenters = [];

        $builder = new Builder();
        $builder->getAugmenters()->walk(function ($augmenter) use (&$augmenters): void {
            $rc = new \ReflectionClass($augmenter);
            $augmenters[] = $this->collectAugmenterData($rc);
        });

        return $augmenters;
    }

    protected function collectAugmenterData(\ReflectionClass $rc): array
    {
        $classDoc = $this->parseDocblock($rc->getDocComment());
        $description = preg_replace('/\n?@implements\s+[^\n]+/', '', $classDoc['content']);

        return [
            'name' => $rc->getShortName(),
            'description' => trim($description),
            'configPrefix' => lcfirst($rc->getShortName()) . '.',
            'options' => $this->collectOptions($rc),
            'see' => $classDoc['see'],
        ];
    }
}
