<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Tools\Docs\ProcGenerator;

$gen = new ProcGenerator(__DIR__ . '/../');

ob_start();

echo $gen->preamble('Processors');

echo PHP_EOL . '## Processor Configuration' . PHP_EOL;

echo '### Command line' . PHP_EOL;
echo <<< EOT
The `-c` option allows to specify a name/value pair with the name consisting
of the processor name (starting lowercase) and  option name separated by a dot (`.`).

```shell
> ./vendor/bin/openapi -c operatinId.hash=true // ...
> ./vendor/bin/openapi -c pathFilter.tags[]=/pets/ -c pathFilter.tags[]=/store/ // ...
```


EOT;

echo '### Programmatically with PHP' . PHP_EOL;
echo <<< EOT
Configuration can be set using the `Generator::setConfig()` method. Keys can either be the same
as on the command line or be broken down into nested arrays.

```php
(new Generator())
    ->setConfig([
        'operationId.hash' => true,
        'pathFilter' => [
            'tags' => [
                '/pets/',
                '/store/',
            ],
        ],
    ]);
```


EOT;

echo PHP_EOL . '## Default Processors' . PHP_EOL;
foreach ($gen->getProcessorsDetails() as $ii => $details) {
    $off = $ii + 1;
    echo $gen->formatClassHeader($details['name'], 'Processors');
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

file_put_contents($gen->docPath('reference/processors.md'), ob_get_clean());
