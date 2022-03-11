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
        $documentation = $this->extractDocumentation($rc->getDocComment());
        echo $documentation['content'] . PHP_EOL;

        $parameters = $rctor->getParameters();
        if ($parameters) {
            echo PHP_EOL . '#### Parameters' . PHP_EOL;
            echo '<dl>' . PHP_EOL;
            foreach ($parameters as $rp) {
                if ($var = $this->getParameterType($fqdn, $rp)) {
                    $var = ' : <span style="font-family: monospace;">' . $var . '</span>';
                }

                echo '  <dt><strong>' . $rp->getName() . '</strong>' . $var . '</dt>' . PHP_EOL;
                echo '  <dd>' . self::NO_DETAILS_AVAILABLE . '</dd>' . PHP_EOL;
            }
            echo '</dl>' . PHP_EOL;
        }

        if ($documentation['see']) {
            echo PHP_EOL . '#### Reference' . PHP_EOL;
            foreach ($documentation['see'] as $link) {
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
        $documentation = $this->extractDocumentation($rc->getDocComment());
        echo $documentation['content'] . PHP_EOL;

        // todo: anchestor properties
        $properties = array_filter($details[$fqdn]['properties'], function ($property) use ($fqdn) {
            return !in_array($property, $fqdn::$_blacklist) && $property[0] != '_';
        });

        if ($properties) {
            echo PHP_EOL . '#### Properties' . PHP_EOL;
            echo '<dl>' . PHP_EOL;
            foreach ($properties as $property) {
                $rp = new ReflectionProperty($fqdn, $property);
                $propertyDocumentation = $this->extractDocumentation($rp->getDocComment());
                if ($var = $this->getPropertyType($fqdn, $property, $propertyDocumentation['var'])) {
                    $var = ' : <span style="font-family: monospace;">' . $var . '</span>';
                }

                echo '  <dt><strong>' . $property . '</strong>' . $var . '</dt>' . PHP_EOL;
                echo '  <dd>' . nl2br($propertyDocumentation['content'] ?: self::NO_DETAILS_AVAILABLE) . '</dd>' . PHP_EOL;
            }
            echo '</dl>' . PHP_EOL;
        }

        if ($documentation['see']) {
            echo PHP_EOL . '#### Reference' . PHP_EOL;
            foreach ($documentation['see'] as $link) {
                echo '- ' . $link . PHP_EOL;
            }
        }

        echo PHP_EOL;

        return ob_get_clean();
    }

    protected function getParameterType(string $fqdn, ReflectionParameter $rp): string
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

        return implode('|', array_unique($var));
    }

    protected function getPropertyType(string $fqdn, string $property, string $dockblockVar): string
    {
        $var = [];

        $rp = new ReflectionProperty($fqdn, $property);
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
        } elseif ($dockblockVar) {
            $var[] = htmlentities($dockblockVar);
        }

        return implode('|', array_unique($var));
    }
    static $vars = [];

    protected function extractDocumentation($docblock): array
    {
        if (!$docblock) {
            return ['content' => '', 'see' => [], 'var' => ''];
        }

        $comment = preg_split('/(\n|\r\n)/', (string)$docblock);

        $comment[0] = preg_replace('/[ \t]*\\/\*\*/', '', $comment[0]); // strip '/**'
        $i = count($comment) - 1;
        $comment[$i] = preg_replace('/\*\/[ \t]*$/', '', $comment[$i]); // strip '*/'

        $see = [];
        $var = '';
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

        return ['content' => $content, 'see' => $see, 'var' => $var];
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
