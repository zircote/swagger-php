<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs;

use OpenApi\Analysers\TokenScanner;
use OpenApi\Annotations\AbstractAnnotation;

class RefGenerator extends DocGenerator
{
    public const ATTRIBUTES = 'Attributes';
    public const ANNOTATIONS = 'Annotations';

    protected $scanner;

    public function __construct($projectRoot)
    {
        parent::__construct($projectRoot);

        $this->scanner = new TokenScanner();
    }

    public function classesForType(string $type): array
    {
        $classes = [];
        $dir = new \DirectoryIterator($this->projectRoot . '/src/' . $type);
        foreach ($dir as $entry) {
            if (!$entry->isFile() || $entry->getExtension() != 'php') {
                continue;
            }
            $class = $entry->getBasename('.php');
            if (in_array($class, ['AbstractAnnotation', 'Operation', 'ParameterTrait', 'OperationTrait'])) {
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

    public function types(): array
    {
        return [self::ANNOTATIONS, self::ATTRIBUTES];
    }

    public function formatAttributesDetails(string $name, string $fqdn, string $filename): string
    {
        $rctor = (new \ReflectionClass($fqdn))->getMethod('__construct');

        ob_start();

        $rc = new \ReflectionClass($fqdn);
        $classDocumentation = $this->extractDocumentation($rc->getDocComment());
        echo $classDocumentation['content'] . PHP_EOL;

        $ctorDocumentation = $this->extractDocumentation($rc->getMethod('__construct')->getDocComment());
        $params = $ctorDocumentation['params'];

        $this->treeDetails($fqdn);

        $parameters = $rctor->getParameters();
        if ($parameters) {
            echo PHP_EOL . '#### Parameters' . PHP_EOL;
            echo '---' . PHP_EOL;

            echo '<dl>' . PHP_EOL;
            foreach ($parameters as $rp) {
                $parameter = $rp->getName();
                $propertyDocumentation = $this->getPropertyDocumentation($fqdn, $parameter);
                $def = array_key_exists($parameter, $params)
                    ? $params[$parameter]['type']
                    : '';

                if ($var = $this->getReflectionType($fqdn, $rp, true, $def)) {
                    $var = ' : <span style="font-family: monospace;">' . $var . '</span>';
                }

                echo '  <dt><strong>' . $parameter . '</strong>' . $var . '</dt>' . PHP_EOL;
                echo '  <dd>';
                $this->propertyDetails($propertyDocumentation);
                echo '</dd>' . PHP_EOL;
            }
            echo '</dl>' . PHP_EOL;
        }

        if ($classDocumentation['see']) {
            echo PHP_EOL . '#### Reference' . PHP_EOL;
            echo '---' . PHP_EOL;

            foreach ($classDocumentation['see'] as $link) {
                echo '- ' . $link . PHP_EOL;
            }
        }

        echo PHP_EOL;

        return ob_get_clean();
    }

    /**
     * @param class-string<AbstractAnnotation> $fqdn
     */
    public function formatAnnotationsDetails(string $name, string $fqdn, string $filename): string
    {
        $details = $this->scanner->scanFile($filename);

        ob_start();

        $rc = new \ReflectionClass($fqdn);
        $classDocumentation = $this->extractDocumentation($rc->getDocComment());
        echo $classDocumentation['content'] . PHP_EOL;

        $this->treeDetails($fqdn);

        $nestedProps = $this->getNestedProperties($fqdn);
        $properties = array_filter($details[$fqdn]['properties'], function ($property) use ($fqdn, $nestedProps) {
            return !in_array($property, $fqdn::$_blacklist) && $property[0] != '_' && !in_array($property, $nestedProps);
        });

        if ($properties) {
            echo PHP_EOL . '#### Properties' . PHP_EOL;
            echo '---' . PHP_EOL;

            echo '<dl>' . PHP_EOL;
            foreach ($properties as $property) {
                $rp = new \ReflectionProperty($fqdn, $property);
                $propertyDocumentation = $this->getPropertyDocumentation($fqdn, $property);
                if ($var = $this->getReflectionType($fqdn, $rp, false, $propertyDocumentation['var'])) {
                    $var = ' : <span style="font-family: monospace;">' . $var . '</span>';
                }

                echo '  <dt><strong>' . $property . '</strong>' . $var . '</dt>' . PHP_EOL;
                echo '  <dd>';
                $this->propertyDetails($propertyDocumentation);
                echo '</dd>' . PHP_EOL;
            }
            echo '</dl>' . PHP_EOL;
        }

        if ($classDocumentation['see']) {
            echo PHP_EOL . '#### Reference' . PHP_EOL;
            echo '---' . PHP_EOL;

            foreach ($classDocumentation['see'] as $link) {
                echo '- ' . $link . PHP_EOL;
            }
        }

        echo PHP_EOL;

        return ob_get_clean();
    }

    // ------------------------------------------------------------------------

    protected function getPropertyDocumentation(string $fqdn, string $name): array
    {
        /** @var class-string<AbstractAnnotation> $class */
        $class = str_replace('Attributes', 'Annotations', $fqdn);
        try {
            $rp = new \ReflectionProperty($class, $name);
        } catch (\ReflectionException $re) {
            $rp = null;
        }

        $documentation = $this->extractDocumentation($rp ? $rp->getDocComment() : null);

        $documentation['required'] = in_array($name, $class::$_required);

        return $documentation;
    }

    protected function propertyDetails(array $propertyDocumentation): void
    {
        echo '<p>' . nl2br($propertyDocumentation['content'] ?: self::NO_DETAILS_AVAILABLE) . '</p>';

        echo '<table class="table-plain">';
        echo '<tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>' . ($propertyDocumentation['required'] ? 'yes' : 'no') . '</b></td></tr>';

        if ($propertyDocumentation['see']) {
            $links = [];
            foreach ($propertyDocumentation['see'] as $see) {
                if ($link = $this->linkFromMarkup($see)) {
                    $links[] = $link;
                }
            }
            if ($links) {
                echo '<tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;">' . implode(', ', $links) . '</td></tr>';
            }
        }

        echo '</table>';
    }

    /**
     * @param class-string<AbstractAnnotation> $fqdn
     */
    protected function getNestedProperties($fqdn): array
    {
        $props = [];
        foreach ($fqdn::$_nested as $details) {
            $props[] = ((array) $details)[0];
        }

        return $props;
    }

    /**
     * @param class-string<AbstractAnnotation> $fqdn
     */
    protected function treeDetails($fqdn)
    {
        if ($fqdn::$_parents) {
            echo PHP_EOL . '#### Allowed in' . PHP_EOL;
            echo '---' . PHP_EOL;

            $parents = array_map(function (string $parent) {
                $shortName = $this->shortName($parent);

                return '<a href="#' . strtolower($shortName) . '">' . $shortName . '</a>';
            }, $fqdn::$_parents);
            echo implode(', ', $parents) . PHP_EOL;
        }

        if ($fqdn::$_nested) {
            echo PHP_EOL . '#### Nested elements' . PHP_EOL;
            echo '---' . PHP_EOL;

            $nested = array_map(function (string $nested) {
                $shortName = $this->shortName($nested);

                return '<a href="#' . strtolower($shortName) . '">' . $shortName . '</a>';
            }, array_keys($fqdn::$_nested));
            echo implode(', ', $nested) . PHP_EOL;
        }
    }

    protected function shortName(string $class): string
    {
        return str_replace(['OpenApi\\Annotations\\', 'OpenApi\\Attributes\\'], '', $class);
    }

    protected function getReflectionType(string $fqdn, $rp, bool $preferDefault = false, string $def = ''): string
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

        return implode('|', array_map(function ($item) {
            return htmlentities($item);
        }, array_unique($var)));
    }
}
