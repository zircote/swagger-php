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

    protected function renderConfigSection(): string
    {
        $out = "\n" . $this->renderer->sectionHeader('Augmenter Configuration');

        $out .= "\n### Command line\n";
        $out .= <<<'EOT'
The `-c` option allows to specify a name/value pair with the name consisting
of the augmenter name (starting lowercase) and option name separated by a dot (`.`).

```shell
> ./vendor/bin/openapi --mode spec -c operationId.hash=true // ...
> ./vendor/bin/openapi --mode spec -c pathFilter.tags[]=/pets/ -c pathFilter.tags[]=/store/ // ...
```

EOT;

        $out .= "\n### Programmatically with PHP\n";

        return $out . <<<'EOT'
Configuration can be set using the `Builder::withAugmenters()` method to access the pipeline
and configure individual augmenters via `Pipeline::get()`.

```php
(new Builder())
    ->withAugmenters(function ($pipeline) {
        $pipeline->get(Augmenter\OperationId::class)->setHash(true);
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

    /**
     * @return list<array{name: string, type: string, default: string, description: string}>
     */
    protected function collectOptions(\ReflectionClass $rc): array
    {
        $options = [];

        foreach ($rc->getMethods() as $method) {
            if (!str_starts_with($method->getName(), 'set')) {
                continue;
            }

            if ($method->getName() === 'setLogger') {
                continue;
            }

            $pname = lcfirst(substr($method->getName(), 3));
            $type = 'n/a';
            if (1 === count($method->getParameters())) {
                if ($rt = $method->getParameters()[0]->getType()) {
                    $type = $rt->getName();
                }
            }

            $phpdoc = $this->parseDocblock($method->getDocComment());
            $description = '';
            if ($phpdoc['content']) {
                $description = $phpdoc['content'];
            } elseif (array_key_exists($pname, $phpdoc['params']) && $phpdoc['params'][$pname]['content']) {
                $description = $phpdoc['params'][$pname]['content'];
            }

            $default = $this->resolveDefault($rc, $pname);

            $options[] = [
                'name' => $pname,
                'type' => $type,
                'default' => $default,
                'description' => $description,
            ];
        }

        return $options;
    }

    protected function resolveDefault(\ReflectionClass $rc, string $pname): string
    {
        if (!$rc->hasMethod('__construct')) {
            return 'N/A';
        }

        $cc = $rc->getMethod('__construct');
        foreach ($cc->getParameters() as $parameter) {
            if ($parameter->getName() === $pname) {
                if (!$parameter->isDefaultValueAvailable()) {
                    return 'N/A';
                }

                $dv = $parameter->getDefaultValue();

                return match (gettype($dv)) {
                    'NULL' => 'null',
                    'boolean' => $dv ? 'true' : 'false',
                    'array' => '[' . implode(', ', $dv) . ']',
                    'object' => $dv::class,
                    default => (string) $dv,
                };
            }
        }

        return 'N/A';
    }
}
