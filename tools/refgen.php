<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Tools\Docs\RefGenerator;

$refgen = new RefGenerator(__DIR__ . '/../');

foreach ($refgen->types() as $type) {
    ob_start();

    echo $refgen->preamble($type);

    foreach ($refgen->classesForType($type) as $name => $details) {
        echo $refgen->formatClassHeader($name, $type);
        $method = "format{$type}Details";
        echo $refgen->$method($name, $details['fqdn'], $details['filename']);
    }

    file_put_contents($refgen->docPath('reference/' . strtolower($type) . '.md'), ob_get_clean());
}
