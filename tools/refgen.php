<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Tools\Docs\RefGenerator;

$gen = new RefGenerator(__DIR__ . '/../');

foreach ($gen->types() as $type) {
    ob_start();

    echo $gen->preamble($type);
    echo PHP_EOL . "## $type" . PHP_EOL;

    foreach ($gen->classesForType($type) as $name => $details) {
        echo $gen->formatClassHeader($name, $type);
        $method = "format{$type}Details";
        echo $gen->$method($name, $details['fqdn'], $details['filename']);
    }

    file_put_contents($gen->docPath('reference/' . strtolower($type) . '.md'), ob_get_clean());
}
