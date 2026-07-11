<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Tools\Docs\Reference\AttributeGenerator;
use OpenApi\Tools\Docs\Reference\AugmenterGenerator;
use OpenApi\Tools\Docs\Reference\ExampleGenerator;
use OpenApi\Tools\Docs\Reference\ProcessorGenerator;

$projectRoot = __DIR__ . '/../';

$generators = [
    'ref' => new AttributeGenerator($projectRoot),
    'proc' => new ProcessorGenerator($projectRoot),
    'aug' => new AugmenterGenerator($projectRoot),
    'example' => new ExampleGenerator($projectRoot),
];

$requested = array_slice($argv, 1);
if ($requested) {
    $generators = array_intersect_key($generators, array_flip($requested));
}

$outputMap = [
    'annotations' => 'reference/annotations.md',
    'attributes' => 'reference/attributes.md',
    'processors' => 'reference/processors.md',
    'augmenters' => 'reference/augmenters.md',
    'examples' => 'guide/examples.md',
];

foreach ($generators as $name => $generator) {
    echo "Running {$name} generator...\n";

    foreach ($generator->generate() as $key => $content) {
        if (!isset($outputMap[$key])) {
            echo "  Warning: unknown output key '{$key}'\n";
            continue;
        }

        $path = $generator->docPath($outputMap[$key]);
        file_put_contents($path, $content);
        echo "  Wrote $outputMap[$key]\n";
    }
}

echo "Done.\n";
