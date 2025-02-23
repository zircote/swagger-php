<?php declare(strict_types=1);

use OpenApi\Tools\Docs\ExampleGenerator;

require_once __DIR__ . '/../vendor/autoload.php';

$gen = new ExampleGenerator(__DIR__ . '/../');

ob_start();
echo $gen->preamble('Example');

foreach (['api', 'petstore'] as $name) {
    $exampleFolder = $gen->examplePath("specs/$name");

    echo PHP_EOL;
    if (file_exists("$exampleFolder/Readme.md")) {
        echo file_get_contents("$exampleFolder/Readme.md") . PHP_EOL;
    } else {
        echo '## ' . $name . PHP_EOL;
    }

    $typeFiles = [];
    foreach (['annotations', 'attributes'] as $type) {
        $typeFolder = $gen->examplePath("specs/$name/$type");
        $typeFiles[$type] = $gen->collectFiles($typeFolder, '*.php');
    }

    // use annotations as reference... might flip eventually...
    foreach ($typeFiles['annotations'] as $relFilename => $filename) {
        echo '### ' . $relFilename . PHP_EOL . PHP_EOL;

        if (array_key_exists($relFilename, $typeFiles['attributes'])) {
            echo <<< EOB
<codeblock id="$name-$filename">
  <template v-slot:at>

<<< @/examples/specs/$name/attributes/$relFilename

  </template>
  <template v-slot:an>

<<< @/examples/specs/$name/annotations/$relFilename

  </template>
</codeblock>


EOB;
        }
    }
}

file_put_contents($gen->docPath('guide/examples.md'), ob_get_clean());
