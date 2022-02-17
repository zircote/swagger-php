<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Composer\Autoload\ClassLoader;

$exampleDir = __DIR__ . '/../Examples';

$classloader = new ClassLoader();
$classloader->addPsr4('OpenApi\\Examples\\SwaggerSpec\\PetstoreSimple\\', $exampleDir . '/swagger-spec/petstore-simple');
$classloader->register();
