<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Reference;

use OpenApi\Spec as OA;
use OpenApi\Tools\Docs\DocGenerator;
use OpenApi\Tools\Docs\Sections\AllowedInSection;
use OpenApi\Tools\Docs\Sections\DescriptionSection;
use OpenApi\Tools\Docs\Sections\NestedElementsSection;
use OpenApi\Tools\Docs\Sections\ParametersSection;
use OpenApi\Tools\Docs\Sections\ReferencesSection;
use OpenApi\Tools\Docs\Sections\SectionInterface;

class SpecAttributeGenerator extends DocGenerator
{
    public function generate(): array
    {
        $classes = $this->discoverClasses();
        [$parentMap, $nestedMap] = $this->buildMaps($classes);

        $content = $this->renderer->preamble(
            'Spec Attribute',
            $this->snippetContent('spec-attributes'),
        );

        $content .= "\n" . $this->renderer->sectionHeader('Spec Attributes');

        foreach ($classes as $shortName => $fqdn) {
            $content .= "\n" . $this->renderClassLink($shortName, $fqdn);
            $data = $this->collectClassData($shortName, $fqdn, $parentMap, $nestedMap);
            $content .= $this->renderSections($data);
        }

        return ['spec-attributes' => $content];
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
     * @return array<string,class-string<OA\AbstractAttribute>>
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
     * Build maps from contained(): parentMap (child FQDN => parents) and nestedMap (parent FQDN => children).
     *
     * @param array<string,class-string<OA\AbstractAttribute>> $classes
     *
     * @return array{array<string,list<array{name: string, anchor: string}>>, array<string,list<array{name: string, anchor: string}>>}
     */
    protected function buildMaps(array $classes): array
    {
        $parentMap = [];
        $nestedMap = [];

        foreach ($classes as $shortName => $fqdn) {
            $instance = (new \ReflectionClass($fqdn))->newInstanceWithoutConstructor();
            foreach ($instance->contained() as $parentClass => $prop) {
                $parentShortName = $this->shortName($parentClass);
                $parentMap[$fqdn][] = ['name' => $parentShortName, 'anchor' => $this->anchor($parentShortName)];
                $nestedMap[$parentClass][] = ['name' => $shortName, 'anchor' => $this->anchor($shortName)];
            }
        }

        return [$parentMap, $nestedMap];
    }

    /**
     * @param array<string,list<array{name: string, anchor: string}>> $parentMap
     * @param array<string,list<array{name: string, anchor: string}>> $nestedMap
     *
     * @return array<string,mixed>
     */
    protected function collectClassData(string $shortName, string $fqdn, array $parentMap, array $nestedMap): array
    {
        $rc = new \ReflectionClass($fqdn);
        $classDoc = $this->parseDocblock($rc->getDocComment());
        $ctorDoc = $rc->hasMethod('__construct')
            ? $this->parseDocblock($rc->getMethod('__construct')->getDocComment())
            : ['content' => '', 'see' => [], 'var' => '', 'params' => []];

        $instance = $rc->newInstanceWithoutConstructor();
        $nested = $nestedMap[$fqdn] ?? [];
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

        $parts = $this->typeNames($type);

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
