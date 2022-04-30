<?php declare(strict_types=1);

use OpenApi\Analysers\TokenScanner;
use OpenApi\Annotations\AbstractAnnotation;

require_once __DIR__ . '/../vendor/autoload.php';

class RefGenerator
{
    const ATTRIBUTES = 'Attributes';
    const ANNOTATIONS = 'Annotations';
    const NO_DETAILS_AVAILABLE = 'No details available.';

    protected $scanner;

    public function __construct()
    {
        $this->scanner = new TokenScanner();
    }

    /**
     *
     */
    public function preamble(string $type): string
    {
        return <<< EOT
# $type

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)

In addition to this page, there are also a number of [examples](https://github.com/zircote/swagger-php/tree/master/Examples#readme) which might help you out.



EOT;
    }

    /**
     *
     */
    public function classesForType(string $type): array
    {
        $classes = [];
        $dir = new DirectoryIterator(__DIR__ . '/../src/' . $type);
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

    /**
     *
     */
    public function types(): array
    {
        return [self::ANNOTATIONS, self::ATTRIBUTES];
    }

    /**
     *
     */
    public function formatHeader(string $name, string $type): string
    {
        return <<< EOT
## [$name](https://github.com/zircote/swagger-php/tree/master/src/$type/$name.php)


EOT;
    }

    /**
     *
     */
    public function formatAttributesDetails(string $name, string $fqdn, string $filename): string
    {
        $rctor = (new ReflectionClass($fqdn))->getMethod('__construct');

        ob_start();

        $rc = new ReflectionClass($fqdn);
        $classDocumentation = $this->extractDocumentation($rc->getDocComment());
        echo $classDocumentation['content'] . PHP_EOL;

        $ctorDocumentation = $this->extractDocumentation($rc->getMethod('__construct')->getDocComment());
        $params = $ctorDocumentation['params'];

        $this->treeDetails($fqdn);

        $parameters = $rctor->getParameters();
        if ($parameters) {
            echo PHP_EOL . '#### Parameters' . PHP_EOL;
            echo '---'.PHP_EOL;

            echo '<dl>' . PHP_EOL;
            foreach ($parameters as $rp) {
                $parameter = $rp->getName();
                $def = array_key_exists($parameter, $params)
                    ? $params[$parameter]
                    : '';

                if ($var = $this->getReflectionType($fqdn, $rp, true, $def)) {
                    $var = ' : <span style="font-family: monospace;">' . $var . '</span>';
                }

                echo '  <dt><strong>' . $parameter . '</strong>' . $var . '</dt>' . PHP_EOL;
                echo '  <dd>';
                echo '<p>' . self::NO_DETAILS_AVAILABLE . '</p>';
                echo '</dd>' . PHP_EOL;
            }
            echo '</dl>' . PHP_EOL;
        }

        if ($classDocumentation['see']) {
            echo PHP_EOL . '#### Reference' . PHP_EOL;
            echo '---'.PHP_EOL;

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

        $rc = new ReflectionClass($fqdn);
        $classDocumentation = $this->extractDocumentation($rc->getDocComment());
        echo $classDocumentation['content'] . PHP_EOL;

        $this->treeDetails($fqdn);

        $nestedProps = $this->getNestedProperties($fqdn);
        $properties = array_filter($details[$fqdn]['properties'], function ($property) use ($fqdn, $nestedProps) {
            return !in_array($property, $fqdn::$_blacklist) && $property[0] != '_' && !in_array($property, $nestedProps);
        });

        if ($properties) {
            echo PHP_EOL . '#### Properties' . PHP_EOL;
            echo '---'.PHP_EOL;

            echo '<dl>' . PHP_EOL;
            foreach ($properties as $property) {
                $rp = new ReflectionProperty($fqdn, $property);
                $propertyDocumentation = $this->extractDocumentation($rp->getDocComment());
                if ($var = $this->getReflectionType($fqdn, $rp, false, $propertyDocumentation['var'])) {
                    $var = ' : <span style="font-family: monospace;">' . $var . '</span>';
                }

                echo '  <dt><strong>' . $property . '</strong>' . $var . '</dt>' . PHP_EOL;
                echo '  <dd>';
                echo '<p>' . nl2br($propertyDocumentation['content'] ?: self::NO_DETAILS_AVAILABLE) . '</p>';
                if ($propertyDocumentation['see']) {
                    $links = [];
                    foreach ($propertyDocumentation['see'] as $see) {
                        if ($link = $this->linkFromMarkup($see)) {
                            $links[] = $link;
                        }
                    }
                    if ($links) {
                        echo '<p><i>See</i>: ' . implode(', ', $links) . '</p>';
                    }
                }

                echo '</dd>' . PHP_EOL;
            }
            echo '</dl>' . PHP_EOL;
        }

        if ($classDocumentation['see']) {
            echo PHP_EOL . '#### Reference' . PHP_EOL;
            echo '---'.PHP_EOL;

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
    protected function getNestedProperties($fqdn): array
    {
        $props = [];
        foreach ($fqdn::$_nested as $details) {
            $props[] = ((array)$details)[0];
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
            echo '---'.PHP_EOL;

            $parents = array_map(function (string $parent) {
                $shortName = $this->shortName($parent);
                return '<a href="#' . strtolower($shortName) . '">' . $shortName . '</a>';
            }, $fqdn::$_parents);
            echo implode(', ', $parents) . PHP_EOL;
        }

        if ($fqdn::$_nested) {
            echo PHP_EOL . '#### Nested elements' . PHP_EOL;
            echo '---'.PHP_EOL;

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

    protected function linkFromMarkup(string $see): ?string
    {
        preg_match('/\[([^]]+)]\((.*)\)/', $see, $matches);

        return 3 == count($matches) ? '<a href="' . $matches[2] . '">' . $matches[1] . '</a>' : null;
    }

    protected function getReflectionType(string $fqdn, $rp, bool $preferDefault = false, string $def = ''): string
    {
        $var = [];

        if ($type = $rp->getType()) {
            if ($type instanceof ReflectionUnionType) {
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

    protected function extractDocumentation($docblock): array
    {
        if (!$docblock) {
            return ['content' => '', 'see' => [], 'var' => '', 'params' => []];
        }

        $comment = preg_split('/(\n|\r\n)/', (string)$docblock);

        $comment[0] = preg_replace('/[ \t]*\\/\*\*/', '', $comment[0]); // strip '/**'
        $i = count($comment) - 1;
        $comment[$i] = preg_replace('/\*\/[ \t]*$/', '', $comment[$i]); // strip '*/'

        $see = [];
        $var = '';
        $params = [];
        $contentLines = [];
        $append = false;
        foreach ($comment as $line) {
            $line = ltrim($line, "\t *");
            if (substr($line, 0, 1) === '@') {
                if (substr($line, 0, 5) === '@see ') {
                    $see[] = trim(substr($line, 5));
                }
                if (substr($line, 0, 5) === '@var ') {
                    $var = trim(substr($line, 5));
                }
                if (substr($line, 0, 7) === '@param ') {
                    preg_match('/^([^\$]+)\$(.+)$/', trim(substr($line, 7)), $match);
                    if (3 == count($match)) {
                        $params[trim($match[2])] = trim($match[1]);
                    }
                }
                continue;
            }

            if ($append) {
                $i = count($contentLines) - 1;
                $contentLines[$i] = substr($contentLines[$i], 0, -1) . $line;
            } else {
                $contentLines[] = $line;
            }
            $append = (substr($line, -1) === '\\');
        }
        $content = trim(implode("\n", $contentLines));

        return ['content' => $content, 'see' => $see, 'var' => $var, 'params' => $params];
    }
}


// ================================================================================
$refgen = new RefGenerator();

foreach ($refgen->types() as $type) {
    ob_start();

    echo $refgen->preamble($type);
    foreach ($refgen->classesForType($type) as $name => $details) {
        echo $refgen->formatHeader($name, $type);
        $method = "format{$type}Details";
        echo $refgen->$method($name, $details['fqdn'], $details['filename']);
    }

    file_put_contents(__DIR__ . '/reference/' . strtolower($type) . '.md', ob_get_clean());
}
