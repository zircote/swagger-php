<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Reference;

use OpenApi\Resolver;
use OpenApi\Tools\Docs\DocGenerator;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\TypedList;

/**
 * What runs by default at each extension point.
 *
 * Discovery walks the live defaults rather than a directory, so the page lists what actually
 * ships and in the order it runs. Augmenters and compilers are documented elsewhere and are
 * linked rather than repeated.
 */
class ExtensionPointGenerator extends DocGenerator
{
    public function generate(): array
    {
        $content = $this->renderer->preamble(
            'Extension Point',
            $this->snippetContent('extension-points'),
        );

        $content .= "\n" . $this->renderer->sectionHeader('Default Translators');
        foreach ($this->collect($this->translators()) as $data) {
            $content .= "\n" . $this->renderer->classHeader($data['name'], 'Assembler');
            $content .= $this->renderSections($data);
        }

        $content .= "\n" . $this->renderer->sectionHeader('Default Resolvers');
        foreach ($this->collect($this->resolvers()) as $data) {
            $content .= "\n" . $this->renderer->classHeader($data['name'], 'Resolver');
            $content .= $this->renderSections($data);
        }

        return ['extension-points' => $content];
    }

    /**
     * @return list<object>
     */
    protected function translators(): array
    {
        return iterator_to_array((new AttributeFactory())->getTranslators());
    }

    /**
     * @return list<object>
     */
    protected function resolvers(): array
    {
        $resolvers = [];
        (new Resolver())->withResolvers(function (TypedList $list) use (&$resolvers): void {
            $resolvers = iterator_to_array($list);
        });

        return $resolvers;
    }

    /**
     * @param  list<object>                                                                                                                                                                    $instances
     * @return list<array{name: string, description: string, configPrefix: string, options: list<array{name: string, type: string, default: string, description: string}>, see: list<string>}>
     */
    protected function collect(array $instances): array
    {
        $collected = [];

        foreach ($instances as $instance) {
            $rc = new \ReflectionClass($instance);
            $classDoc = $this->parseDocblock($rc->getDocComment());
            $description = preg_replace('/\n?@phpstan-\w+[^\n]+/', '', $classDoc['content']);

            $collected[] = [
                'name' => $rc->getShortName(),
                'description' => trim((string) $description),
                'configPrefix' => lcfirst($rc->getShortName()) . '.',
                'options' => $this->collectOptions($rc),
                'see' => $classDoc['see'],
            ];
        }

        return $collected;
    }
}
