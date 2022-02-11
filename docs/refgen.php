<?php declare(strict_types=1);

use OpenApi\Analysers\TokenScanner;

require_once __DIR__.'/../vendor/autoload.php';

class RefGenerator
{
    const ATTRIBUTES = 'Attributes';
    const ANNOTATIONS = 'Annotations';

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
        $lctype = strtolower($type);

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
    public function formatHeader(string $name, string $fqdn, string $type): string
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
        $rctor = (new \ReflectionClass($fqdn))->getMethod('__construct');

        ob_start();

        echo '#### Properties'.PHP_EOL;
        foreach ($rctor->getParameters() as $rp) {
            echo '- ' . $rp->getName().PHP_EOL;
        }

        return ob_get_clean();
    }

    /**
     *
     */
    public function formatAnnotationsDetails(string $name, string $fqdn, string $filename): string
    {
        $details = $this->scanner->scanFile($filename);

        ob_start();

        echo '#### Properties'.PHP_EOL;
        foreach ($details[$fqdn]['properties'] as $property) {
            if (in_array($property, $fqdn::$_blacklist) || $property[0] == '_') {
                continue;
            }
            echo '- ' . $property.PHP_EOL;
        }

        return ob_get_clean();
    }
}


// ================================================================================
$refgen = new RefGenerator();

foreach ($refgen->types() as $type) {
    ob_start();
    $lctype = strtolower($type);

    echo $refgen->preamble($type);
    foreach ($refgen->classesForType($type) as $name => $details) {
        echo $refgen->formatHeader($name, $details['fqdn'], $type);
        $method = "format{$type}Details";
        echo $refgen->$method($name, $details['fqdn'], $details['filename']);
    }
    file_put_contents(__DIR__ . '/reference/' . strtolower($type) . '.md', ob_get_clean());
}
