<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Reference;

use OpenApi\Builder;
use OpenApi\Tools\Docs\DocGenerator;
use OpenApi\Tools\Docs\Renderer;
use OpenApi\Tools\Docs\Sections\ConfigSettingsSection;
use OpenApi\Tools\Docs\Sections\DescriptionSection;
use OpenApi\Tools\Docs\Sections\ReferencesSection;
use OpenApi\Tools\Docs\Sections\SectionInterface;

class AugmenterGenerator extends DocGenerator
{
    /** @var list<SectionInterface> */
    protected array $sections;

    public function __construct(string $projectRoot, ?Renderer $renderer = null)
    {
        parent::__construct($projectRoot, $renderer);

        $this->sections = $this->defaultSections();
    }

    /**
     * @param list<SectionInterface> $sections
     */
    public function setSections(array $sections): static
    {
        $this->sections = $sections;

        return $this;
    }

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

            foreach ($this->sections as $section) {
                $rendered = $section->render($data);
                if ($rendered !== '') {
                    $content .= "\n" . $rendered;
                }
            }
        }

        return ['augmenters' => $content];
    }

    /**
     * @return list<SectionInterface>
     */
    protected function defaultSections(): array
    {
        return [
            new DescriptionSection(),
            new ConfigSettingsSection(),
            new ReferencesSection(),
        ];
    }

    protected function renderConfigSection(): string
    {
        $out = "\n" . $this->renderer->sectionHeader('Augmenter Configuration');

        $out .= "\n### Command line\n";
        $out .= <<<'EOT'
The `-c` option takes a name/value pair: the augmenter name (starting lowercase)
and the option name, separated by a dot (`.`).

To list the available augmenter names and options use `-D`. It still requires a
source path, e.g. `./vendor/bin/openapi --mode spec -D src`. Unknown keys are
reported as warnings.

```shell
> ./vendor/bin/openapi --mode spec -c operationIds.hash=true src
> ./vendor/bin/openapi --mode spec -c pathFilter.tags[]=/pets/ -c pathFilter.tags[]=/store/ src
```

EOT;

        $out .= "\n### Programmatically with PHP\n";

        return $out . <<<'EOT'
Configuration can be set using the `Builder::withAugmenters()` method to access the pipeline
and configure individual augmenters via `Pipeline::get()`.

```php
(new Builder())
    ->withAugmenters(function ($pipeline) {
        $pipeline->get(Augmenter\OperationIds::class)->setHash(true);
        $pipeline->get(Augmenter\PathFilter::class)->setTags(['/pets/', '/store/']);
    });
```

EOT;
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
