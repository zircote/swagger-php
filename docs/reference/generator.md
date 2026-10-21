# Using the `Generator` <Badge type="warning" text="deprecated" />

## Introduction
The `Generator` class is classic mode's engine. The entry point for every mode is the
[`Builder`](builder.md), which runs the `Generator` in classic mode and hands it to
[`withGenerator()`](builder.md#generator-configuration-classic-mode) for configuration.

## The `\OpenApi\Generator` class

The `Generator` class provides an object-oriented (and fluent) API to generate OpenApi specs
and allows for easy customization where needed.

## Full example of using the `Generator` class to generate OpenApi specs

```php
require('vendor/autoload.php');

$validate = true;
$logger = new \Psr\Log\NullLogger();
$processors = [/* my processors */];
$finder = \Symfony\Component\Finder\Finder::create()->files()->name('*.php')->in(__DIR__);

$context = new OpenApi\Context();

$openapi = (new \OpenApi\Generator($logger))
            ->setProcessorPipeline(new \OpenApi\Utils\Pipeline($processors))
            ->setAliases(['MY' => 'My\Annotations'])
            ->setNamespaces(['My\\Annotations\\'])
            ->setAnalyser(new \OpenApi\Analysers\ReflectionAnalyser([new OpenApi\Analysers\DocBlockAnnotationFactory(), new OpenApi\Analysers\AttributeAnnotationFactory()]))
            ->setVersion(\OpenApi\Annotations\OpenApi::VERSION_3_0_0)
            ->generate(['/path1/to/project', $finder], new \OpenApi\Analysis([], $context), $validate);
```

`Aliases` and `namespaces` setting are optional and only required when using annotations.
They allow to customize the parsing of docblocks and are passed through to the underlying `doctrine/annotations` library.

Defaults:
* **aliases**: `['oa' => 'OpenApi\\Annotations']`

  Aliases help to parse annotations. Effectively, they avoid having to write `use OpenApi\Annotations as OA;` in your code
  and make `@OA\property(..)` annotations still work.

* **namespaces**: `['OpenApi\\Annotations\\']`

  Namespaces control which annotation namespaces can be autoloaded automatically. Under the hood this
  is handled by registering a custom loader with the `doctrine annotation library`.


## Basic example
```php
require('"vendor/autoload.php');

$openapi = (new \OpenApi\Generator())->generate(['/path1/to/project']);
```

The `generate()` method does not support the CLI `exclude` and `pattern` options directly.
Instead, a custom Symfony `Finder` class (`SourceFinder`) can be used that understands these options.

```php
$exclude = ['tests'];
$pattern = '*.php';

$openapi = (new \OpenApi\Generator())->generate(new \OpenApi\Utils\SourceFinder(__DIR__, $exclude, $pattern));
```

## Configuration

`setConfig()` sets generator and processor options. Keys are the ones `-c` takes on the command line, either as
dotted strings or as nested arrays.

```php
$openapi = (new \OpenApi\Generator())
    ->setConfig([
        'operationId.hash' => false,
        'pathFilter' => [
            'tags' => ['/pets/'],
        ],
    ])
    ->generate(['/path/to/project']);
```

The options for each processor are listed in the [Processor reference](/reference/processors). `getConfig()` returns
the configuration in effect, defaults included.

## Running code in the context of a generator

`withContext()` calls a callable with the generator, an empty `Analysis` and the root `Context`. The context carries
the generator's version and logger. This allows parts of the library, such as the analyser, to be used without a full
`generate()` run.

```php
use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;

$schemas = (new Generator())
    ->withContext(function (Generator $generator, Analysis $analysis, Context $context): array {
        return $generator->getAnalyser()
            ->fromFile('/path/to/project/Pet.php', $context)
            ->getAnnotationsOfType(OA\Schema::class, true);
    });
```

`withContext()` returns whatever the callable returns.
