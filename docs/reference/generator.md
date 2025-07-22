# Using the `Generator`

## Introduction
The `Generator` class is the main entry point into `swagger-php`.

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
            ->setProcessorPipeline(new \OpenApi\Pipeline($processors))
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

The `generate()` method does not support the CLI `exclude` and `pattern` options directly. Instead, a Symfony `Finder` instance can be passed in
as source directly.

If needed, the `\OpenApi\Util` class provides a builder method that undertands these options.

```php
$exclude = ['tests'];
$pattern = '*.php';

$openapi = (new \OpenApi\Generator())->generate(\OpenApi\Util::finder(__DIR__, $exclude, $pattern));
```
