<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Reference;

use OpenApi\Spec as OA;
use OpenApi\Tools\Docs\DocGenerator;
use OpenApi\Tools\Docs\Renderer;
use OpenApi\Tools\Docs\Sections\AllowedInSection;
use OpenApi\Tools\Docs\Sections\DescriptionSection;
use OpenApi\Tools\Docs\Sections\NestedElementsSection;
use OpenApi\Tools\Docs\Sections\ParametersSection;
use OpenApi\Tools\Docs\Sections\ReferencesSection;
use OpenApi\Tools\Docs\Sections\SectionInterface;

class SpecAttributeGenerator extends DocGenerator
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
            new AllowedInSection(),
            new NestedElementsSection(),
            new ParametersSection(),
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
        $classes = $this->discoverClasses();
        $parentMap = $this->buildParentMap($classes);

        $content = $this->renderer->preamble(
            'Spec Attribute',
            $this->snippetContent('spec-attributes'),
        );

        $content .= "\n" . $this->renderer->sectionHeader('Spec Attributes');

        foreach ($classes as $shortName => $fqdn) {
            $content .= "\n" . $this->renderClassLink($shortName, $fqdn);
            $data = $this->collectClassData($shortName, $fqdn, $parentMap);

            foreach ($this->sections as $section) {
                $rendered = $section->render($data);
                if ($rendered !== '') {
                    $content .= "\n" . $rendered;
                }
            }
        }

        return ['spec-attributes' => $content];
    }

    /**
     * @return array<string,class-string<AbstractAttribute>>
     */
    protected function discoverClasses(): array
    {
        $classes = [];
        $specDir = $this->projectRoot . '/src/Spec';

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($specDir, \FilesystemIterator::SKIP_DOTS),
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace($specDir . '/', '', $file->getPathname());
            $classPath = str_replace(['/', '.php'], ['\\', ''], $relativePath);
            $fqdn = 'OpenApi\\Spec\\' . $classPath;

            if (!class_exists($fqdn)) {
                continue;
            }

            $rc = new \ReflectionClass($fqdn);
            if ($rc->isAbstract() || !$rc->isSubclassOf(OA\AbstractAttribute::class)) {
                continue;
            }

            $shortName = $this->shortName($fqdn);
            $classes[$shortName] = $fqdn;
        }

        ksort($classes);

        return $classes;
    }

    /**
     * Build a map: child FQDN => list of parent short names (who can contain this class).
     *
     * @param array<string,class-string<AbstractAttribute>> $classes
     *
     * @return array<string,list<array{name: string, anchor: string}>>
     */
    protected function buildParentMap(array $classes): array
    {
        $map = [];

        foreach ($classes as $shortName => $fqdn) {
            $instance = (new \ReflectionClass($fqdn))->newInstanceWithoutConstructor();
            foreach ($instance->contains() as $childClass => $prop) {
                $map[$childClass][] = ['name' => $shortName, 'anchor' => $this->anchor($shortName)];
            }
        }

        return $map;
    }

    /**
     * @param array<string,list<array{name: string, anchor: string}>> $parentMap
     *
     * @return array<string,mixed>
     */
    protected function collectClassData(string $shortName, string $fqdn, array $parentMap): array
    {
        $rc = new \ReflectionClass($fqdn);
        $classDoc = $this->parseDocblock($rc->getDocComment());
        $ctorDoc = $rc->hasMethod('__construct')
            ? $this->parseDocblock($rc->getMethod('__construct')->getDocComment())
            : ['content' => '', 'see' => [], 'var' => '', 'params' => []];

        $instance = $rc->newInstanceWithoutConstructor();
        $nested = $this->collectNested($instance);
        $parents = $parentMap[$fqdn] ?? $this->collectMergeParents($instance);
        $parameters = $this->collectParameters($rc, $ctorDoc);

        return [
            'description' => $classDoc['content'],
            'parents' => $parents,
            'nested' => $nested,
            'parameters' => $parameters,
            'see' => $classDoc['see'],
        ];
    }

    /**
     * @return list<array{name: string, type: string, description: string, see: list<string>}>
     */
    protected function collectParameters(\ReflectionClass $rc, array $ctorDoc): array
    {
        if (!$rc->hasMethod('__construct')) {
            return [];
        }

        $parameters = [];
        $rctor = $rc->getMethod('__construct');

        foreach ($rctor->getParameters() as $rp) {
            $name = $rp->getName();

            if (in_array($name, ['x', 'attachables'], true)) {
                continue;
            }

            $paramDoc = $ctorDoc['params'][$name] ?? null;
            $description = $paramDoc['content'] ?? '';
            $type = $this->resolveType($rp, $paramDoc['type'] ?? '');
            $see = [];

            $parameters[] = [
                'name' => $name,
                'type' => $type,
                'description' => trim($description),
                'see' => $see,
            ];
        }

        return $parameters;
    }

    /**
     * @return list<array{name: string, anchor: string}>
     */
    protected function collectNested(OA\AbstractAttribute $instance): array
    {
        $nested = [];

        foreach ($instance->contains() as $childClass => $prop) {
            $shortName = $this->shortName($childClass);
            $nested[] = ['name' => $shortName, 'anchor' => $this->anchor($shortName)];
        }

        return $nested;
    }

    /**
     * @return list<array{name: string, anchor: string}>
     */
    protected function collectMergeParents(OA\AbstractAttribute $instance): array
    {
        $parents = [];

        foreach ($instance->merge() as $parentClass => $prop) {
            $shortName = $this->shortName($parentClass);
            $parents[] = ['name' => $shortName, 'anchor' => $this->anchor($shortName)];
        }

        return $parents;
    }

    protected function resolveType(\ReflectionParameter $rp, string $docType): string
    {
        if ($docType) {
            return htmlentities($docType);
        }

        $type = $rp->getType();
        if (!$type) {
            return '';
        }

        $parts = [];
        if ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $t) {
                $parts[] = $t->getName();
            }
        } elseif ($type instanceof \ReflectionIntersectionType) {
            foreach ($type->getTypes() as $t) {
                $parts[] = $t->getName();
            }
        } else {
            $parts[] = $type->getName();
        }

        if ($type->allowsNull() && !in_array('null', $parts, true)) {
            $parts[] = 'null';
        }

        return implode('|', array_map(htmlentities(...), $parts));
    }

    protected function renderClassLink(string $shortName, string $fqdn): string
    {
        $relativePath = str_replace(['OpenApi\\', '\\'], ['', '/'], $fqdn) . '.php';

        return "### [{$shortName}](https://github.com/zircote/swagger-php/tree/master/src/{$relativePath})\n";
    }

    protected function shortName(string $fqdn): string
    {
        return str_replace('OpenApi\\Spec\\', '', $fqdn);
    }

    protected function anchor(string $shortName): string
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $shortName));
    }
}
