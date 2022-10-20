<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Tools\Docs\ProcGenerator;

$procgen = new ProcGenerator(__DIR__ . '/../');

ob_start();

echo $procgen->preamble('Processors');

foreach ($procgen->getProcessorDetails() as $ii => $details) {
    $off = $ii + 1;
    echo $procgen->formatClassHeader($details['name'], 'Processors');
    if ($details['properties']) {
        $configPrefix = lcfirst($details['name']).'.';
        echo '### Config settings' . PHP_EOL;
        foreach ($details['properties'] as $name => $type) {
            $var = ' : <span style="font-family: monospace;">' . $type . '</span>';
            echo '<dl>' . PHP_EOL;
            echo '  <dt><strong>' . $configPrefix.$name . '</strong>' . $var . '</dt>' . PHP_EOL;
            echo '  <dd>';
            echo '<p>' . nl2br(ProcGenerator::NO_DETAILS_AVAILABLE) . '</p>' . PHP_EOL;
            echo '  </dd>' . PHP_EOL;
            echo '</dl>' . PHP_EOL;
        }
        echo PHP_EOL;
    }
}

file_put_contents($procgen->docPath('reference/processors.md'), ob_get_clean());
