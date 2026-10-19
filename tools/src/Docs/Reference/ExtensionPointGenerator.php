<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Reference;

use OpenApi\Contracts\AttributeTranslatorInterface;
use OpenApi\Contracts\ResolverInterface;
use OpenApi\Resolver;
use OpenApi\Tools\Docs\DocGenerator;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\TypedList;

/**
 * What runs by default at each extension point.
 *
 * Discovery walks the live defaults rather than a directory, so the page lists what actually
 * runs and in the order it runs. Augmenters, mergers and compilers are documented elsewhere and
 * are linked rather than repeated.
 */
class ExtensionPointGenerator extends DocGenerator
{
    /**
     * @return array<string, string>
     */
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
     * @return list<AttributeTranslatorInterface>
     */
    protected function translators(): array
    {
        return array_values(iterator_to_array((new AttributeFactory())->getTranslators()));
    }

    /**
     * @return list<ResolverInterface>
     */
    protected function resolvers(): array
    {
        $resolvers = [];
        (new Resolver())->withResolvers(
            /** @param TypedList<ResolverInterface> $list */
            function (TypedList $list) use (&$resolvers): void {
                $resolvers = array_values(iterator_to_array($list));
            }
        );

        return $resolvers;
    }
}
