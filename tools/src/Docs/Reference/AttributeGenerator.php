<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Reference;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Tools\Docs\DocGenerator;
use OpenApi\Tools\Docs\Renderer;
use OpenApi\Utils\TokenScanner;

class AttributeGenerator extends DocGenerator
{
    protected TokenScanner $scanner;

    public function __construct(string $projectRoot, ?Renderer $renderer = null)
    {
        parent::__construct($projectRoot, $renderer);

        $this->scanner = new TokenScanner();
    }

    public function generate(): array
    {
        $output = [];

        foreach ($this->types() as $type) {
            $content = $this->renderer->preamble(
                rtrim((string) $type, 's'),
                $this->snippetContent($type),
            );
            $content .= "\n" . $this->renderer->sectionHeader($type);

            $paramHeading = $type === 'Annotations' ? 'Properties' : 'Parameters';

            foreach ($this->classesForType($type) as $name => $details) {
                $content .= "\n" . $this->renderer->classHeader($name, $type);
                $method = "collect{$type}Details";
                $data = $this->$method($name, $details['fqdn'], $details['filename']);
                $content .= $this->renderClassDetails($data, $paramHeading);
            }

            $output[strtolower((string) $type)] = $content;
        }

        return $output;
    }

    public function types(): array
    {
        return ['Annotations', 'Attributes'];
    }

    public function classesForType(string $type): array
    {
        $classes = [];
        $dir = new \DirectoryIterator($this->projectRoot . '/src/' . $type);
        foreach ($dir as $entry) {
            if (!$entry->isFile() || $entry->getExtension() !== 'php') {
                continue;
            }
            $class = $entry->getBasename('.php');
            if (in_array($class, ['AbstractAnnotation', 'Operation', 'ParameterTrait', 'OperationTrait', 'JsonSchemaTrait'])) {
                continue;
            }
            $classes[$class] = [
                'fqdn' => 'OpenApi\\' . $type . '\\' . $class,
                'filename' => $entry->getPathname(),
            ];
        }

        ksort($classes);

        return $classes;
    }

    /**
     * @return array{description: string, parents: list<array{name: string, anchor: string}>, nested: list<array{name: string, anchor: string}>, parameters: list<array{name: string, type: string, description: string, required: bool, see: list<string>}>, see: list<string>}
     */
    protected function collectAttributesDetails(string $name, string $fqdn, string $filename): array
    {
        $rc = new \ReflectionClass($fqdn);
        $classDoc = $this->parseDocblock($rc->getDocComment());
        $ctorDoc = $this->parseDocblock($rc->getMethod('__construct')->getDocComment());

        $parameters = [];
        $rctor = $rc->getMethod('__construct');
        foreach ($rctor->getParameters() as $rp) {
            $paramName = $rp->getName();
            $propertyDoc = $this->getPropertyDocumentation($fqdn, $paramName);
            $def = array_key_exists($paramName, $ctorDoc['params'])
                ? $ctorDoc['params'][$paramName]['type']
                : '';

            $parameters[] = [
                'name' => $paramName,
                'type' => $this->getReflectionType($fqdn, $rp, true, $def),
                'description' => $propertyDoc['content'],
                'required' => $propertyDoc['required'],
                'see' => $propertyDoc['see'],
            ];
        }

        return [
            'description' => $classDoc['content'],
            'parents' => $this->collectParents($fqdn),
            'nested' => $this->collectNested($fqdn),
            'parameters' => $parameters,
            'see' => $classDoc['see'],
        ];
    }

    /**
     * @return array{description: string, parents: list<array{name: string, anchor: string}>, nested: list<array{name: string, anchor: string}>, parameters: list<array{name: string, type: string, description: string, required: bool, see: list<string>}>, see: list<string>}
     */
    protected function collectAnnotationsDetails(string $name, string $fqdn, string $filename): array
    {
        $details = $this->scanner->scanFile($filename);
        $rc = new \ReflectionClass($fqdn);
        $classDoc = $this->parseDocblock($rc->getDocComment());

        $nestedProps = $this->getNestedProperties($fqdn);
        $properties = array_filter($details[$fqdn]['properties'], fn (string $property): bool => !in_array($property, $fqdn::$_blacklist) && $property[0] != '_' && !in_array($property, $nestedProps));

        $parameters = [];
        foreach ($properties as $property) {
            $rp = new \ReflectionProperty($fqdn, $property);
            $propertyDoc = $this->getPropertyDocumentation($fqdn, $property);

            $parameters[] = [
                'name' => $property,
                'type' => $this->getReflectionType($fqdn, $rp, false, $propertyDoc['var']),
                'description' => $propertyDoc['content'],
                'required' => $propertyDoc['required'],
                'see' => $propertyDoc['see'],
            ];
        }

        return [
            'description' => $classDoc['content'],
            'parents' => $this->collectParents($fqdn),
            'nested' => $this->collectNested($fqdn),
            'parameters' => $parameters,
            'see' => $classDoc['see'],
        ];
    }

    protected function renderClassDetails(array $data, string $paramHeading = 'Parameters'): string
    {
        $out = '';
        $out .= $this->renderer->classDescription($data['description']);

        $sections = [
            $this->renderer->allowedIn($data['parents']),
            $this->renderer->nestedElements($data['nested']),
            $this->renderer->parameters($data['parameters'], $paramHeading),
            $this->renderer->references($data['see']),
        ];

        foreach ($sections as $section) {
            if ($section !== '') {
                $out .= "\n" . $section;
            }
        }

        return $out;
    }

    /**
     * @return list<array{name: string, anchor: string}>
     */
    protected function collectParents(string $fqdn): array
    {
        if (!property_exists($fqdn, '_parents') || !$fqdn::$_parents) {
            return [];
        }

        return array_map(function (string $parent): array {
            $shortName = $this->shortName($parent);

            return ['name' => $shortName, 'anchor' => strtolower($shortName)];
        }, $fqdn::$_parents);
    }

    /**
     * @return list<array{name: string, anchor: string}>
     */
    protected function collectNested(string $fqdn): array
    {
        if (!property_exists($fqdn, '_nested') || !$fqdn::$_nested) {
            return [];
        }

        return array_map(function (string $nested): array {
            $shortName = $this->shortName($nested);

            return ['name' => $shortName, 'anchor' => strtolower($shortName)];
        }, array_keys($fqdn::$_nested));
    }

    protected function getPropertyDocumentation(string $fqdn, string $name): array
    {
        /** @var class-string<AbstractAnnotation> $class */
        $class = str_replace('Attributes', 'Annotations', $fqdn);
        try {
            $rp = new \ReflectionProperty($class, $name);
        } catch (\ReflectionException) {
            $rp = null;
        }

        $documentation = $this->parseDocblock($rp ? $rp->getDocComment() : null);
        $documentation['required'] = in_array($name, $class::$_required);

        return $documentation;
    }

    /**
     * @param class-string<AbstractAnnotation> $fqdn
     */
    protected function getNestedProperties(string $fqdn): array
    {
        $props = [];
        foreach ($fqdn::$_nested as $details) {
            $props[] = ((array) $details)[0];
        }

        return $props;
    }

    protected function shortName(string $class): string
    {
        return str_replace(['OpenApi\\Annotations\\', 'OpenApi\\Attributes\\'], '', $class);
    }

    protected function getReflectionType(string $fqdn, \ReflectionProperty|\ReflectionParameter $rp, bool $preferDefault = false, string $def = ''): string
    {
        $var = [];

        if ($type = $rp->getType()) {
            if ($type instanceof \ReflectionUnionType) {
                foreach ($type->getTypes() as $type) {
                    $var[] = $type->getName();
                }
            } else {
                $var[] = $type->getName();
            }
            if ($type->allowsNull()) {
                $var[] = 'null';
            }
        }
        if ($def && (!$var || $preferDefault)) {
            if ($preferDefault) {
                $var = [];
            }
            $var = array_merge($var, explode('|', $def));
        }

        return implode('|', array_map(htmlentities(...), array_unique($var)));
    }
}
