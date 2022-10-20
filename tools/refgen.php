<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Tools\Docs\RefGenerator;

$refgen = new RefGenerator(__DIR__ . '/../');

foreach ($refgen->types() as $type) {
    ob_start();

    echo $refgen->preamble($type);
    echo 'In addition to this page, there are also a number of [examples](https://github.com/zircote/swagger-php/tree/master/Examples#readme) which might help you out.' . PHP_EOL . PHP_EOL;

    foreach ($refgen->classesForType($type) as $name => $details) {
        echo $refgen->formatClassHeader($name, $type);
        $method = "format{$type}Details";
        echo $refgen->$method($name, $details['fqdn'], $details['filename']);
    }

    file_put_contents($refgen->docPath('reference/' . strtolower($type) . '.md'), ob_get_clean());
}
