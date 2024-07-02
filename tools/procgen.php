<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Tools\Docs\ProcGenerator;

$procgen = new ProcGenerator(__DIR__ . '/../');

ob_start();

echo $procgen->preamble('Processors');
echo PHP_EOL . '## Default Processors' . PHP_EOL;

foreach ($procgen->getProcessorsDetails() as $ii => $details) {
    $off = $ii + 1;
    echo $procgen->formatClassHeader($details['name'], 'Processors');
    echo $details['phpdoc']['content'] . PHP_EOL;

    if ($details['options']) {
        $configPrefix = lcfirst($details['name']) . '.';
        echo '#### Config settings' . PHP_EOL;
        foreach ($details['options'] as $name => $odetails) {
            if ($odetails) {
                $var = ' : <span style="font-family: monospace;">' . $odetails['type'] . '</span>';
                $default = ' : <span style="font-family: monospace;">' . $odetails['default'] . '</span>';

                echo '<dl>' . PHP_EOL;
                echo '  <dt><strong>' . $configPrefix . $name . '</strong>' . $var . '</dt>' . PHP_EOL;
                echo '  <dt><strong>default</strong>' . $default . '</dt>' . PHP_EOL;
                echo '  <dd>';
                echo '<p>' . nl2br($odetails['phpdoc'] ? $odetails['phpdoc']['content'] : ProcGenerator::NO_DETAILS_AVAILABLE) . '</p>';
                echo '  </dd>' . PHP_EOL;
                echo '</dl>' . PHP_EOL;
            }
        }
        echo PHP_EOL;

        if ($details['phpdoc']['see']) {
            echo PHP_EOL . '#### Reference' . PHP_EOL;
            echo '---' . PHP_EOL;

            foreach ($details['phpdoc']['see'] as $link) {
                echo '- ' . $link . PHP_EOL;
            }
        }

        echo PHP_EOL;
    }
}

file_put_contents($procgen->docPath('reference/processors.md'), ob_get_clean());
